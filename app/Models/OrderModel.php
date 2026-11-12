<?php
/**
 * OrderModel - Quản lý đơn hàng
 */
class OrderModel {
    /**
     * @var Database
     */
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * @param array $data
     * @return int|string
     */
    public function create($data) {
        $orderNumber = generateOrderNumber();
        
        $inserted = $this->db->query("INSERT INTO orders (order_number, user_id, total_amount, discount_amount, shipping_fee, final_amount, ship_name, ship_phone, ship_email, ship_address, ship_city, ship_district, ship_ward, status, payment_method, payment_status, customer_note) VALUES (:order_number, :user_id, :total_amount, :discount_amount, :shipping_fee, :final_amount, :ship_name, :ship_phone, :ship_email, :ship_address, :ship_city, :ship_district, :ship_ward, 'pending', :payment_method, :payment_status, :customer_note)")
            ->bind(':order_number', $orderNumber)
            ->bind(':user_id', $data['user_id'])
            ->bind(':total_amount', $data['total_amount'])
            ->bind(':discount_amount', $data['discount_amount'] ?? 0)
            ->bind(':shipping_fee', $data['shipping_fee'] ?? 0)
            ->bind(':final_amount', $data['final_amount'])
            ->bind(':ship_name', $data['ship_name'])
            ->bind(':ship_phone', $data['ship_phone'])
            ->bind(':ship_email', $data['ship_email'] ?? null)
            ->bind(':ship_address', $data['ship_address'])
            ->bind(':ship_city', $data['ship_city'] ?? null)
            ->bind(':ship_district', $data['ship_district'] ?? null)
            ->bind(':ship_ward', $data['ship_ward'] ?? null)
            ->bind(':payment_method', $data['payment_method'] ?? 'cod')
            ->bind(':payment_status', $data['payment_method'] === 'cod' ? 'unpaid' : 'unpaid')
            ->bind(':customer_note', $data['customer_note'] ?? null)
            ->execute();
        if (!$inserted) {
            throw new RuntimeException('Order insert failed');
        }
        
        $orderId = $this->db->lastInsertId();
        if ($orderId) {
            $this->db->query("INSERT INTO order_status_history (order_id, from_status, to_status, actor_id, note, created_at) VALUES (:oid, NULL, 'pending', :actor, 'Tạo đơn hàng', NOW())")
                ->bind(':oid', $orderId)->bind(':actor', $data['user_id'] ?? null)->execute();
        }
        return $orderId;
    }

    public function createWithDetails($data, array $items) {
        if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
            $this->db->beginTransaction();
            try {
                $id = $this->create($data);
                if (!$id) throw new RuntimeException('Order insert failed');
                foreach ($items as $item) {
                    if (!$this->addDetail($id, $item)) throw new RuntimeException('Order detail insert failed');
                }
                if (!empty($data['coupon_id']) && (float)$data['discount_amount'] > 0) {
                    $coupon = $this->db->query("SELECT used_count,max_uses FROM coupons WHERE id=:id FOR UPDATE")
                        ->bind(':id', $data['coupon_id'])->single();
                    if (!$coupon) throw new RuntimeException('Coupon not found');
                    if (!empty($coupon['max_uses']) && (int)$coupon['used_count'] >= (int)$coupon['max_uses']) {
                        throw new RuntimeException('Coupon usage limit reached');
                    }
                    $used = $this->db->query("SELECT id FROM coupon_uses WHERE coupon_id=:cid AND user_id=:uid LIMIT 1")
                        ->bind(':cid', $data['coupon_id'])->bind(':uid', $data['user_id'])->single();
                    if ($used) throw new RuntimeException('Coupon already used by user');
                    if (!$this->db->query("INSERT INTO coupon_uses (coupon_id,order_id,user_id,discount_amount) VALUES (:cid,:oid,:uid,:amount)")
                        ->bind(':cid', $data['coupon_id'])->bind(':oid', $id)->bind(':uid', $data['user_id'])->bind(':amount', $data['discount_amount'])->execute()) {
                        throw new RuntimeException('Coupon usage insert failed');
                    }
                    if (!$this->db->query("UPDATE coupons SET used_count=used_count+1 WHERE id=:id AND (max_uses IS NULL OR used_count<max_uses)")
                        ->bind(':id', $data['coupon_id'])->execute()) {
                        throw new RuntimeException('Coupon usage update failed');
                    }
                }
                $this->db->commit();
                return $id;
            } catch (Throwable $e) {
                $this->db->rollBack();
                error_log('Order transaction failed: ' . $e->getMessage());
                return false;
            }
        }

        $orderNumber = generateOrderNumber();
        $sql = "/*atomic*/ SET XACT_ABORT ON; BEGIN TRY BEGIN TRANSACTION;
            INSERT INTO orders (order_number,user_id,total_amount,discount_amount,shipping_fee,final_amount,ship_name,ship_phone,ship_email,ship_address,ship_city,ship_district,ship_ward,status,payment_method,payment_status,customer_note)
            VALUES (:order_number,:user_id,:total_amount,:discount_amount,:shipping_fee,:final_amount,:ship_name,:ship_phone,:ship_email,:ship_address,:ship_city,:ship_district,:ship_ward,'pending',:payment_method,'unpaid',:customer_note);
            DECLARE @new_order_id INT = CONVERT(INT,SCOPE_IDENTITY());
            INSERT INTO order_status_history (order_id,from_status,to_status,actor_id,note,created_at) VALUES (@new_order_id,NULL,'pending',:actor_id,N'Order created',CURRENT_TIMESTAMP);";
        $params = [
            ':order_number'=>$orderNumber, ':user_id'=>$data['user_id'], ':total_amount'=>$data['total_amount'],
            ':discount_amount'=>$data['discount_amount'] ?? 0, ':shipping_fee'=>$data['shipping_fee'] ?? 0,
            ':final_amount'=>$data['final_amount'], ':ship_name'=>$data['ship_name'], ':ship_phone'=>$data['ship_phone'],
            ':ship_email'=>$data['ship_email'] ?? null, ':ship_address'=>$data['ship_address'], ':ship_city'=>$data['ship_city'] ?? null,
            ':ship_district'=>$data['ship_district'] ?? null, ':ship_ward'=>$data['ship_ward'] ?? null,
            ':payment_method'=>$data['payment_method'] ?? 'cod', ':customer_note'=>$data['customer_note'] ?? null,
            ':actor_id'=>$data['user_id'] ?? null,
        ];
        foreach (array_values($items) as $index => $item) {
            $product = ':p'.$index; $name = ':n'.$index; $qty = ':q'.$index; $price = ':pr'.$index;
            $discount = ':d'.$index; $subtotal = ':st'.$index; $variantText = ':v'.$index; $variantId = ':vid'.$index; $sku = ':sku'.$index; $variantName = ':vn'.$index;
            $sql .= " INSERT INTO order_details (order_id,product_id,product_name,quantity,price,discount,subtotal,variant,variant_id,variant_sku,variant_name)
                VALUES (@new_order_id,$product,$name,$qty,$price,$discount,$subtotal,$variantText,$variantId,$sku,$variantName);";
            $params[$product] = $item['product_id']; $params[$name] = $item['product_name']; $params[$qty] = $item['quantity'];
            $params[$price] = $item['price']; $params[$discount] = $item['discount'] ?? 0; $params[$subtotal] = $item['subtotal'];
            $params[$variantText] = $item['variant_name'] ?? ($item['variant'] ?? null);
            $params[$variantId] = $item['variant_id'] ?? null; $params[$sku] = $item['variant_sku'] ?? null;
            $params[$variantName] = $item['variant_name'] ?? ($item['variant'] ?? null);
        }
        $sql .= " COMMIT TRANSACTION; SELECT @new_order_id AS last_id; END TRY BEGIN CATCH IF XACT_STATE()<>0 ROLLBACK TRANSACTION; THROW; END CATCH;";
        $stmt = $this->db->query($sql);
        foreach ($params as $key => $value) $stmt->bind($key, $value);
        if (!$stmt->execute()) return false;
        return $this->db->lastInsertId();
    }
    
    /**
     * @param int|string $orderId
     * @param array $item
     * @return bool
     */
    public function addDetail($orderId, $item) {
        $variant = !empty($item['variant_name']) ? trim($item['variant_name']) : (!empty($item['variant']) ? trim($item['variant']) : null);
        $productName = $item['product_name'];
        if (!empty($variant) && strpos($productName, $variant) === false) {
            $productName .= ' (' . $variant . ')';
        }
        $res = $this->db->query("INSERT INTO order_details (order_id,product_id,product_name,quantity,price,discount,subtotal,variant,variant_id,variant_sku,variant_name) VALUES (:order_id,:product_id,:product_name,:quantity,:price,:discount,:subtotal,:variant,:variant_id,:variant_sku,:variant_name)")
            ->bind(':order_id', $orderId)
            ->bind(':product_id', $item['product_id'])
            ->bind(':product_name', $productName)
            ->bind(':quantity', $item['quantity'])
            ->bind(':price', $item['price'])
            ->bind(':discount', $item['discount'] ?? 0)
            ->bind(':subtotal', $item['subtotal'])
            ->bind(':variant', $variant)
            ->bind(':variant_id', $item['variant_id'] ?? null)
            ->bind(':variant_sku', $item['variant_sku'] ?? null)
            ->bind(':variant_name', $item['variant_name'] ?? $variant)
            ->execute();

        // Cập nhật kho bằng PHP nếu đang chạy trên hosting (không hỗ trợ Triggers)
        if ($res && defined('DB_TYPE') && DB_TYPE === 'mysql') {
            if (!empty($item['variant_id'])) {
                $variantUpdate = $this->db->query("UPDATE product_variants SET stock_quantity=stock_quantity-:qty WHERE id=:variant_id AND stock_quantity>=:check_qty")
                    ->bind(':qty', $item['quantity'])->bind(':variant_id', $item['variant_id'])->bind(':check_qty', $item['quantity']);
                if (!$variantUpdate->execute() || $variantUpdate->rowCount() !== 1) return false;
            }
            $productUpdate = $this->db->query("UPDATE products SET stock_quantity=stock_quantity-:qty WHERE id=:pid AND stock_quantity>=:check_qty")
                ->bind(':qty', $item['quantity'])->bind(':pid', $item['product_id'])->bind(':check_qty', $item['quantity']);
            if (!$productUpdate->execute() || $productUpdate->rowCount() !== 1) return false;
                
            $inventoryUpdate = $this->db->query("UPDATE inventory SET quantity=quantity-:qty WHERE product_id=:pid AND quantity>=:check_qty")
                ->bind(':qty', $item['quantity'])->bind(':pid', $item['product_id'])->bind(':check_qty', $item['quantity']);
            if (!$inventoryUpdate->execute() || $inventoryUpdate->rowCount() !== 1) return false;
                
            $this->db->query("INSERT INTO inventory_logs (product_id, transaction_type, quantity, reference_type, notes) VALUES (:pid, 'adjust', :qty, 'system_adjust', :notes)")
                ->bind(':pid', $item['product_id'])
                ->bind(':qty', -$item['quantity'])
                ->bind(':notes', 'Xuất kho cho đơn hàng ID #' . $orderId)
                ->execute();
        }

        return $res;
    }
    
    /**
     * @param int|string $id
     * @return array|false
     */
    public function findById($id) {
        return $this->db->query("SELECT o.*, u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = :id")
            ->bind(':id', $id)->single();
    }
    
    /**
     * @param string $orderNumber
     * @return array|false
     */
    public function findByOrderNumber($orderNumber) {
        $orderNumber = ltrim(trim($orderNumber), '#');
        return $this->db->query("SELECT o.*, u.full_name as customer_name, u.email as customer_email FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE LTRIM(RTRIM(o.order_number)) = :on")
            ->bind(':on', $orderNumber)->single();
    }
    
    /**
     * @param int|string $orderId
     * @return array
     */
    public function getDetails($orderId) {
        return $this->db->query("SELECT od.*, p.main_image, p.slug as product_slug FROM order_details od LEFT JOIN products p ON od.product_id = p.id WHERE od.order_id = :id")
            ->bind(':id', $orderId)->resultSet();
    }

    public function createVnpayAttempt($orderId, $txnRef, $amount, $createDate) {
        $stmt = $this->db->query("INSERT INTO payments (order_id,payment_method,amount,provider_txn_ref,provider_transaction_date,status,notes) VALUES (:order_id,'vnpay',:amount,:txn_ref,:create_date,'pending','Awaiting signed VNPay IPN')")
            ->bind(':order_id', $orderId)->bind(':amount', $amount)->bind(':txn_ref', $txnRef)->bind(':create_date', $createDate);
        return $stmt->execute();
    }

    public function recordVnpayPayment($order, array $gateway) {
        $txnRef = (string)($gateway['vnp_TxnRef'] ?? '');
        $amount = ((int)($gateway['vnp_Amount'] ?? 0)) / 100;
        $txnNo = (string)($gateway['vnp_TransactionNo'] ?? '');
        if ($txnRef === '' || $txnNo === '') return false;
        $stmt = $this->db->query("UPDATE payments SET transaction_code=:txn_no,provider_transaction_no=:txn_no,status='completed',payment_date=CURRENT_TIMESTAMP,bank_name=:bank,notes='Confirmed by signed VNPay IPN',updated_at=CURRENT_TIMESTAMP WHERE order_id=:order_id AND payment_method='vnpay' AND provider_txn_ref=:txn_ref AND amount=:amount AND status IN ('pending','completed')")
            ->bind(':txn_no', $txnNo)->bind(':bank', (string)($gateway['vnp_BankCode'] ?? ''))
            ->bind(':order_id', $order['id'])->bind(':txn_ref', $txnRef)->bind(':amount', $amount);
        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    public function failVnpayAttempt($orderId, $txnRef) {
        return $this->db->query("UPDATE payments SET status='failed',updated_at=CURRENT_TIMESTAMP WHERE order_id=:order_id AND payment_method='vnpay' AND provider_txn_ref=:txn_ref AND status='pending'")
            ->bind(':order_id', $orderId)->bind(':txn_ref', $txnRef)->execute();
    }

    public function getVnpayPayment($orderId) {
        $limit = (defined('DB_TYPE') && DB_TYPE === 'mysql') ? ' LIMIT 1' : '';
        $top = (defined('DB_TYPE') && DB_TYPE === 'mysql') ? '' : ' TOP 1';
        return $this->db->query("SELECT{$top} * FROM payments WHERE order_id=:id AND payment_method='vnpay' AND status='completed' AND provider_txn_ref IS NOT NULL ORDER BY id DESC{$limit}")
            ->bind(':id', $orderId)->single();
    }

    public function getVnpayRefunds($orderId) {
        return $this->db->query("SELECT r.*,u.full_name AS requester_name FROM vnpay_refunds r LEFT JOIN users u ON u.id=r.requested_by WHERE r.order_id=:id ORDER BY r.created_at DESC")
            ->bind(':id', $orderId)->resultSet();
    }

    public function getRefundedAmount($orderId) {
        $row = $this->db->query("SELECT COALESCE(SUM(amount),0) AS amount FROM vnpay_refunds WHERE order_id=:id AND state='succeeded'")
            ->bind(':id', $orderId)->single();
        return (float)($row['amount'] ?? 0);
    }

    public function createVnpayRefundRequest($orderId, $amount, $requestId, $actorId, $reason = '') {
        $order = $this->findById($orderId);
        $payment = $this->getVnpayPayment($orderId);
        if (!$order || !in_array($order['status'], ['return_requested','return_received'], true) || $order['payment_status'] !== 'paid' || !$payment) return false;
        $summary = $this->db->query("SELECT COALESCE(SUM(CASE WHEN state='succeeded' THEN amount ELSE 0 END),0) AS refunded, SUM(CASE WHEN state IN ('requested','processing','unknown') THEN 1 ELSE 0 END) AS active FROM vnpay_refunds WHERE order_id=:id")
            ->bind(':id', $orderId)->single();
        if (!$summary || (int)$summary['active'] > 0 || $amount <= 0 || $amount > ((float)$payment['amount'] - (float)$summary['refunded'])) return false;
        $txnDate = (string)($payment['provider_transaction_date'] ?? '');
        if (!preg_match('/^\d{14}$/', $txnDate)) return false;
        $type = (float)$summary['refunded'] <= 0 && $amount >= (float)$payment['amount'] ? '02' : '03';
        $stmt = $this->db->query("INSERT INTO vnpay_refunds (order_id, request_id, transaction_ref, transaction_no, transaction_date, amount, transaction_type, state, requested_by, reason) VALUES (:order_id,:request_id,:txn_ref,:txn_no,:txn_date,:amount,:type,'requested',:actor,:reason)")
            ->bind(':order_id', $orderId)->bind(':request_id', $requestId)->bind(':txn_ref', $payment['provider_txn_ref'])
            ->bind(':txn_no', $payment['provider_transaction_no'])->bind(':txn_date', $txnDate)
            ->bind(':amount', $amount)->bind(':type', $type)->bind(':actor', $actorId)->bind(':reason', $reason);
        if (!$stmt->execute()) return false;
        return $this->db->lastInsertId() ?: false;
    }

    public function getVnpayRefund($refundId) {
        return $this->db->query("SELECT * FROM vnpay_refunds WHERE id=:id")->bind(':id', $refundId)->single();
    }

    public function updateVnpayRefundResult($refundId, array $result) {
        $state = in_array($result['state'] ?? '', ['processing','succeeded','rejected','unknown'], true) ? $result['state'] : 'unknown';
        $response = $result['response'] ?? null;
        $responseJson = is_array($response) ? json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string)($result['error'] ?? '');
        $code = is_array($response) ? (string)($response['vnp_ResponseCode'] ?? '') : null;
        $txnStatus = is_array($response) ? (string)($response['vnp_TransactionStatus'] ?? '') : null;
        if (defined('DB_TYPE') && DB_TYPE === 'mssql_ps') {
            $sql = "/*atomic*/ SET XACT_ABORT ON; BEGIN TRY BEGIN TRANSACTION;
                UPDATE vnpay_refunds SET state=:state,response_code=:code,transaction_status=:txn_status,response_json=:response,updated_at=CURRENT_TIMESTAMP WHERE id=:id;
                IF :state='succeeded'
                BEGIN
                  DECLARE @oid INT,@actor INT,@refunded DECIMAL(12,2),@final DECIMAL(12,2),@old NVARCHAR(40);
                  SELECT @oid=order_id,@actor=requested_by FROM vnpay_refunds WHERE id=:id;
                  SELECT @refunded=COALESCE(SUM(amount),0) FROM vnpay_refunds WHERE order_id=@oid AND state='succeeded';
                  SELECT @final=final_amount,@old=status FROM orders WHERE id=@oid;
                  IF @refunded>=@final AND @old IN ('return_requested','return_received')
                  BEGIN
                    UPDATE orders SET status='refunded',payment_status='refund',updated_at=CURRENT_TIMESTAMP WHERE id=@oid AND status=@old;
                    IF @@ROWCOUNT=1 INSERT INTO order_status_history(order_id,from_status,to_status,actor_id,note,created_at) VALUES(@oid,@old,'refunded',@actor,N'VNPay confirmed full refund',CURRENT_TIMESTAMP);
                  END;
                END;
                COMMIT TRANSACTION; SELECT 1 AS affected_rows; END TRY BEGIN CATCH IF XACT_STATE()<>0 ROLLBACK TRANSACTION; THROW; END CATCH;";
            $stmt = $this->db->query($sql)->bind(':state', $state)->bind(':code', $code)->bind(':txn_status', $txnStatus)->bind(':response', $responseJson)->bind(':id', $refundId);
            return $stmt->execute() && $stmt->rowCount() > 0;
        }
        $this->db->beginTransaction();
        $ok = $this->db->query("UPDATE vnpay_refunds SET state=:state,response_code=:code,transaction_status=:txn_status,response_json=:response,updated_at=CURRENT_TIMESTAMP WHERE id=:id")
            ->bind(':state', $state)->bind(':code', $code)->bind(':txn_status', $txnStatus)->bind(':response', $responseJson)->bind(':id', $refundId)->execute();
        if ($ok && $state === 'succeeded') {
            $refund = $this->getVnpayRefund($refundId);
            $order = $refund ? $this->findById($refund['order_id']) : false;
            $sum = $refund ? $this->db->query("SELECT SUM(amount) AS refunded FROM vnpay_refunds WHERE order_id=:id AND state='succeeded'")->bind(':id', $refund['order_id'])->single() : false;
            if ($order && (float)($sum['refunded'] ?? 0) >= (float)$order['final_amount'] && in_array($order['status'], ['return_requested','return_received'], true)) {
                $ok = $this->db->query("UPDATE orders SET status='refunded',payment_status='refund',updated_at=CURRENT_TIMESTAMP WHERE id=:id AND status=:old")
                    ->bind(':id', $order['id'])->bind(':old', $order['status'])->execute();
                if ($ok) $ok = $this->db->query("INSERT INTO order_status_history(order_id,from_status,to_status,actor_id,note,created_at) VALUES(:id,:old,'refunded',:actor,'VNPay confirmed full refund',CURRENT_TIMESTAMP)")
                    ->bind(':id', $order['id'])->bind(':old', $order['status'])->bind(':actor', $refund['requested_by'])->execute();
            }
        }
        if ($ok) $this->db->commit(); else $this->db->rollBack();
        return $ok;
    }
    
    /**
     * @param int|string $userId
     * @param string $status
     * @return array
     */
    public function getUserOrders($userId, $status = '') {
        $where = "WHERE o.user_id = :user_id";
        if ($status) $where .= " AND o.status = :status";
        
        $stmt = $this->db->query("SELECT o.*, (SELECT COUNT(*) FROM order_details WHERE order_id = o.id) as item_count FROM orders o $where ORDER BY o.created_at DESC")
            ->bind(':user_id', $userId);
        if ($status) $stmt->bind(':status', $status);
        return $stmt->resultSet();
    }
    
    /**
     * @param int $page
     * @param int $limit
     * @param string $search
     * @param string $status
     * @param string $payment
     * @return array
     */
    public function getAll($page = 1, $limit = 20, $search = '', $status = '', $payment = '') {
        $offset = ($page - 1) * $limit;
        $where = "WHERE 1=1";
        $params = [];
        
        if ($search) {
            $cleanSearch = ltrim(trim($search), '#');
            $where .= " AND (o.order_number LIKE :search OR u.full_name LIKE :search OR u.phone LIKE :search OR o.ship_phone LIKE :search)";
            $params[':search'] = '%' . $cleanSearch . '%';
        }
        if ($status) {
            $where .= " AND o.status = :status";
            $params[':status'] = $status;
        }
        if ($payment) {
            $where .= " AND o.payment_status = :payment";
            $params[':payment'] = $payment;
        }
        
        $stmt = $this->db->query("SELECT o.*, u.full_name as customer_name, u.email as customer_email, (SELECT COUNT(*) FROM order_details WHERE order_id = o.id) as item_count FROM orders o LEFT JOIN users u ON o.user_id = u.id $where ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset")
            ->bind(':limit', $limit)->bind(':offset', $offset);
        foreach ($params as $k => $v) $stmt->bind($k, $v);
        return $stmt->resultSet();
    }
    
    /**
     * @param string $search
     * @param string $status
     * @param string $payment
     * @return int
     */
    public function count($search = '', $status = '', $payment = '') {
        $where = "WHERE 1=1";
        $params = [];
        if ($search) { 
            $cleanSearch = ltrim(trim($search), '#');
            $where .= " AND (o.order_number LIKE :search OR u.full_name LIKE :search)"; 
            $params[':search'] = '%' . $cleanSearch . '%'; 
        }
        if ($status) { $where .= " AND o.status = :status"; $params[':status'] = $status; }
        if ($payment) { $where .= " AND o.payment_status = :payment"; $params[':payment'] = $payment; }
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM orders o LEFT JOIN users u ON o.user_id = u.id $where");
        foreach ($params as $k => $v) $stmt->bind($k, $v);
        $result = $stmt->single();
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * @param int|string $id
     * @param string $status
     * @param string|null $adminNote
     * @return bool
     */
    public function updateStatus($id, $status, $adminNote = null) {
        // Refund completion is gateway controlled and cannot be set by a generic admin status form.
        $allowed = ['pending', 'confirmed', 'processing', 'shipping', 'delivered', 'cancelled', 'return_requested', 'return_received', 'refunded'];
        if (!in_array($status, $allowed, true)) return false;
        $order = $this->findById($id);
        if (!$order || !$this->canTransition($order['status'], $status)) return false;
        if ($order['status'] === $status) {
            if ($adminNote !== null && $adminNote !== '') {
                return $this->db->query("UPDATE orders SET admin_note=:admin_note, updated_at=NOW() WHERE id=:id")
                    ->bind(':admin_note', $adminNote)->bind(':id', $id)->execute();
            }
            return true;
        }
        if (defined('DB_TYPE') && DB_TYPE === 'mssql_ps') {
            return $this->updateStatusSqlServer($id, $order['status'], $status, $adminNote);
        }
        $this->db->beginTransaction();
        $sql = "UPDATE orders SET status=:status, updated_at=NOW()";
        if ($status === 'shipping') $sql .= ", ship_date=NOW()";
        if ($status === 'delivered') $sql .= ", delivery_date=NOW(), payment_status='paid'";
        if ($status === 'refunded') $sql .= ", payment_status='refund'";
        if ($adminNote) $sql .= ", admin_note=:admin_note";
        $sql .= " WHERE id=:id AND status=:from_status";
        
        $stmt = $this->db->query($sql)->bind(':status', $status)->bind(':id', $id)->bind(':from_status', $order['status']);
        if ($adminNote) $stmt->bind(':admin_note', $adminNote);
        $ok = $stmt->execute() && $stmt->rowCount() === 1;
        if ($ok) {
            $historySaved = $this->db->query("INSERT INTO order_status_history (order_id, from_status, to_status, actor_id, note, created_at) VALUES (:oid, :from_status, :to_status, :actor, :note, NOW())")
                ->bind(':oid', $id)->bind(':from_status', $order['status'])->bind(':to_status', $status)
                ->bind(':actor', $_SESSION['user_id'] ?? null)->bind(':note', $adminNote)->execute();
            if (!$historySaved) $ok = false;
            if ($ok && in_array($status, ['cancelled','return_received'], true)) $this->restoreStock($id);
        }
        if ($ok) $this->db->commit(); else $this->db->rollBack();
        return $ok;
    }

    private function updateStatusSqlServer($id, $fromStatus, $status, $adminNote) {
        $set = "status=:to_status,updated_at=CURRENT_TIMESTAMP";
        if ($status === 'shipping') $set .= ',ship_date=CURRENT_TIMESTAMP';
        if ($status === 'delivered') $set .= ",delivery_date=CURRENT_TIMESTAMP,payment_status='paid'";
        if ($status === 'refunded') $set .= ",payment_status='refund'";
        if ($adminNote !== null && $adminNote !== '') $set .= ',admin_note=:admin_note';
        $sql = "/*atomic*/ SET XACT_ABORT ON; BEGIN TRY BEGIN TRANSACTION;
            UPDATE orders SET $set WHERE id=:id AND status=:from_status;
            IF @@ROWCOUNT<>1 BEGIN; THROW 51004, 'Invalid concurrent order status transition.', 1; END;
            INSERT INTO order_status_history (order_id,from_status,to_status,actor_id,note,created_at)
                VALUES (:id,:from_status,:to_status,:actor_id,:note,CURRENT_TIMESTAMP);";
        if (in_array($status, ['cancelled','return_received'], true)) {
            $sql .= "
              UPDATE v SET v.stock_quantity=v.stock_quantity+i.qty,v.updated_at=CURRENT_TIMESTAMP
                FROM product_variants v JOIN (SELECT variant_id,SUM(quantity) qty FROM order_details WHERE order_id=:id AND variant_id IS NOT NULL GROUP BY variant_id) i ON i.variant_id=v.id;
              UPDATE p SET p.stock_quantity=p.stock_quantity+i.qty
                FROM products p JOIN (SELECT product_id,SUM(quantity) qty FROM order_details WHERE order_id=:id GROUP BY product_id) i ON i.product_id=p.id;
              UPDATE inv SET inv.quantity=inv.quantity+i.qty
                FROM inventory inv JOIN (SELECT product_id,SUM(quantity) qty FROM order_details WHERE order_id=:id GROUP BY product_id) i ON i.product_id=inv.product_id;";
        }
        $sql .= " COMMIT TRANSACTION; SELECT 1 AS affected_rows; END TRY BEGIN CATCH IF XACT_STATE()<>0 ROLLBACK TRANSACTION; THROW; END CATCH;";
        $stmt = $this->db->query($sql)->bind(':id', $id)->bind(':to_status', $status)
            ->bind(':from_status', $fromStatus)->bind(':actor_id', $_SESSION['user_id'] ?? null)
            ->bind(':note', $adminNote);
        if ($adminNote !== null && $adminNote !== '') $stmt->bind(':admin_note', $adminNote);
        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    private function canTransition($from, $to) {
        if ($from === $to) return true;
        if (in_array($from, ['cancelled', 'refunded'], true)) return false;

        $operational = ['pending', 'confirmed', 'processing', 'shipping', 'delivered'];
        if (in_array($from, $operational, true) && in_array($to, $operational, true)) return true;

        if ($to === 'cancelled') return true;
        if ($from === 'delivered' && $to === 'return_requested') return true;
        if (in_array($from, ['return_requested', 'return_received'], true)) {
            return in_array($to, ['return_requested', 'return_received', 'refunded', 'delivered', 'cancelled'], true);
        }
        return false;
    }

    private function restoreStock($orderId) {
        foreach ($this->getDetails($orderId) as $item) {
            if (!empty($item['variant_id'])) {
                $this->db->query("UPDATE product_variants SET stock_quantity=stock_quantity+:qty,updated_at=CURRENT_TIMESTAMP WHERE id=:variant_id")
                    ->bind(':qty', (int)$item['quantity'])->bind(':variant_id', $item['variant_id'])->execute();
            }
            $this->db->query("UPDATE products SET stock_quantity = stock_quantity + :qty WHERE id = :pid")
                ->bind(':qty', (int)$item['quantity'])->bind(':pid', $item['product_id'])->execute();
            if ((defined('DB_TYPE') && DB_TYPE === 'mssql_ps') || (defined('APP_ENV') && APP_ENV === 'production')) {
                $this->db->query("UPDATE inventory SET quantity = quantity + :qty WHERE product_id = :pid")
                    ->bind(':qty', (int)$item['quantity'])->bind(':pid', $item['product_id'])->execute();
            }
        }
    }

    public function requestReturn($id, $userId, $reason = '') {
        $order = $this->findById($id);
        if (!$order || (int)$order['user_id'] !== (int)$userId || $order['status'] !== 'delivered') return false;
        return $this->updateStatus($id, 'return_requested', $reason ?: 'Khách yêu cầu đổi trả/hoàn tiền');
    }

    public function getStatusHistory($id) {
        return $this->db->query("SELECT h.*, u.full_name AS actor_name FROM order_status_history h LEFT JOIN users u ON u.id = h.actor_id WHERE h.order_id = :id ORDER BY h.created_at ASC")
            ->bind(':id', $id)->resultSet();
    }

    public function canTransitionTo($id, $status) {
        $order = $this->findById($id);
        return $order ? $this->canTransition($order['status'], $status) : false;
    }
    
    /**
     * @param int|string $id
     * @param string $status
     * @return bool
     */
    public function updatePaymentStatus($id, $status) {
        if ($status === 'paid') {
            $stmt = $this->db->query("UPDATE orders SET payment_status=:status WHERE id=:id AND payment_status<>'paid'")
                ->bind(':status', $status)->bind(':id', $id);
            return $stmt->execute() && $stmt->rowCount() > 0;
        }
        return $this->db->query("UPDATE orders SET payment_status=:status WHERE id=:id")
            ->bind(':status', $status)->bind(':id', $id)->execute();
    }
    
    /**
     * @param int|string $id
     * @param string $method
     * @return bool
     */
    public function updatePaymentMethod($id, $method) {
        return $this->db->query("UPDATE orders SET payment_method=:method WHERE id=:id")
            ->bind(':method', $method)->bind(':id', $id)->execute();
    }
    
    /**
     * @param int|string $id
     * @param string|null $reason
     * @return bool
     */
    public function cancelOrder($id, $reason = null) {
        return $this->updateStatus($id, 'cancelled', $reason);
    }
    
    /**
     * @return array
     */
    public function getStats() {
        if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
            $row = $this->db->query("SELECT 
                COUNT(*) as total, 
                SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending, 
                SUM(CASE WHEN status='shipping' THEN 1 ELSE 0 END) as shipping, 
                SUM(CASE WHEN status='delivered' THEN 1 ELSE 0 END) as delivered, 
                SUM(CASE WHEN status='return_requested' THEN 1 ELSE 0 END) as return_requested,
                SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) as cancelled, 
                SUM(CASE WHEN status='delivered' THEN final_amount ELSE 0.00 END) as total_revenue, 
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_orders, 
                SUM(CASE WHEN DATE(created_at) = CURDATE() AND status='delivered' THEN final_amount ELSE 0.00 END) as today_revenue 
            FROM orders")->single();
        } else {
            $row = $this->db->query("SELECT COUNT(*) as total, SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending, SUM(CASE WHEN status='shipping' THEN 1 ELSE 0 END) as shipping, SUM(CASE WHEN status='delivered' THEN 1 ELSE 0 END) as delivered, SUM(CASE WHEN status='return_requested' THEN 1 ELSE 0 END) as return_requested, SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) as cancelled, SUM(CASE WHEN status='delivered' THEN final_amount ELSE 0 END) as total_revenue, SUM(CASE WHEN CONVERT(date, created_at) = CONVERT(date, GETDATE()) THEN 1 ELSE 0 END) as today_orders, SUM(CASE WHEN CONVERT(date, created_at) = CONVERT(date, GETDATE()) AND status='delivered' THEN final_amount ELSE 0 END) as today_revenue FROM orders")->single();
        }
        
        if (!$row) return [];
        $norm = [];
        foreach ($row as $k => $v) {
            $norm[strtolower($k)] = $v;
        }
        return $norm;
    }
    
    /**
     * @param int $days
     * @return array
     */
    public function getRevenueByDay($days = 30) {
        if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
            $results = $this->db->query("SELECT DATE(created_at) as date, COUNT(*) as order_count, SUM(final_amount) as revenue FROM orders WHERE (status = 'delivered' OR payment_status = 'paid') AND created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY) GROUP BY DATE(created_at) ORDER BY date ASC")
                ->bind(':days', $days)->resultSet();
        } else {
            $results = $this->db->query("SELECT CONVERT(date, created_at) as date, COUNT(*) as order_count, SUM(final_amount) as revenue FROM orders WHERE (status = 'delivered' OR payment_status = 'paid') AND created_at >= DATEADD(day, -:days, GETDATE()) GROUP BY CONVERT(date, created_at) ORDER BY date ASC")
                ->bind(':days', $days)->resultSet();
        }
        return $this->normalizeResultSet($results);
    }
    
    /**
     * @param int|null $year
     * @return array
     */
    public function getRevenueByMonth($year = null) {
        $year = $year ?? date('Y');
        $results = $this->db->query("SELECT MONTH(created_at) as month, COUNT(*) as order_count, SUM(final_amount) as revenue FROM orders WHERE (status = 'delivered' OR payment_status = 'paid') AND YEAR(created_at) = :year GROUP BY MONTH(created_at) ORDER BY month ASC")
            ->bind(':year', $year)->resultSet();
        return $this->normalizeResultSet($results);
    }
    
    /**
     * @param int $limit
     * @return array
     */
    public function getRevenueByYear($limit = 5) {
        $results = $this->db->query("SELECT YEAR(created_at) as year, COUNT(*) as order_count, SUM(final_amount) as revenue FROM orders WHERE (status = 'delivered' OR payment_status = 'paid') GROUP BY YEAR(created_at) ORDER BY year DESC LIMIT :limit")
            ->bind(':limit', $limit)->resultSet();
        return $this->normalizeResultSet($results);
    }
    
    /**
     * Thống kê số lượng đơn và doanh thu theo từng phương thức thanh toán
     *
     * @return array
     */
    public function getPaymentStats() {
        $results = $this->db->query("SELECT 
            payment_method, 
            COUNT(*) as total_orders, 
            SUM(final_amount) as total_amount 
        FROM orders 
        GROUP BY payment_method")->resultSet();
        return $this->normalizeResultSet($results);
    }
    
    /**
     * @param array $results
     * @return array
     */
    private function normalizeResultSet($results) {
        if (!is_array($results)) return [];
        $normalized = [];
        foreach ($results as $row) {
            $normRow = [];
            foreach ($row as $k => $v) {
                $normRow[strtolower($k)] = $v;
            }
            $normalized[] = $normRow;
        }
        return $normalized;
    }
    
    /**
     * @param int|string $orderId
     * @param int|string $userId
     * @return bool
     */
    public function canCancel($orderId, $userId) {
        $order = $this->findById($orderId);
        return $order && $order['user_id'] == $userId && in_array($order['status'], ['pending', 'confirmed']);
    }
    
    /**
     * @param int|string $orderId
     * @param int|string $userId
     * @param int|string $productId
     * @return bool
     */
    public function canReview($orderId, $userId, $productId) {
        $result = $this->db->query("SELECT od.id FROM order_details od INNER JOIN orders o ON od.order_id = o.id WHERE o.id = :order_id AND o.user_id = :user_id AND od.product_id = :product_id AND o.status = 'delivered'")
            ->bind(':order_id', $orderId)->bind(':user_id', $userId)->bind(':product_id', $productId)->single();
        return $result !== false;
    }
}
?>
