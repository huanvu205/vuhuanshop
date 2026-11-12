<?php
/** Server-side Gemini Interactions API client. */
class GeminiService {
    private $apiKey;
    private $model;

    public function __construct($apiKey = null, $model = null) {
        $this->apiKey = $apiKey !== null ? $apiKey : (defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '');
        $this->model = $model !== null ? $model : (defined('GEMINI_MODEL') ? GEMINI_MODEL : 'gemini-3.5-flash-lite');
    }

    public function isConfigured() {
        return $this->apiKey !== '' && function_exists('curl_init');
    }

    public function ask($systemInstruction, $input, $useGoogleSearch = true) {
        if ($this->apiKey === '') {
            return ['ok' => false, 'error' => 'not_configured', 'message' => 'AI chưa được cấu hình.'];
        }
        if (!function_exists('curl_init')) {
            return ['ok' => false, 'error' => 'curl_unavailable', 'message' => 'Máy chủ chưa bật PHP cURL.'];
        }

        $request = [
            'model' => $this->model,
            'system_instruction' => $systemInstruction,
            'input' => $input,
            'generation_config' => [
                'max_output_tokens' => 1400,
                'thinking_level' => 'low',
            ],
            'store' => false,
        ];
        if ($useGoogleSearch) $request['tools'] = [['type' => 'google_search']];

        $handle = curl_init('https://generativelanguage.googleapis.com/v1beta/interactions');
        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-goog-api-key: ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($request, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 28,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $body = curl_exec($handle);
        $curlError = curl_errno($handle);
        $httpCode = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        if ($body === false || $curlError !== 0) {
            return ['ok' => false, 'error' => 'network', 'message' => 'Không kết nối được Gemini.'];
        }

        $decoded = json_decode($body, true);
        if (!is_array($decoded)) {
            return ['ok' => false, 'error' => 'invalid_json', 'message' => 'Gemini trả về dữ liệu không hợp lệ.', 'http_code' => $httpCode];
        }
        if ($httpCode < 200 || $httpCode >= 300) {
            $apiMessage = strtolower((string)($decoded['error']['message'] ?? ''));
            $kind = 'api';
            if ($httpCode === 401 || $httpCode === 403) $kind = 'auth';
            elseif ($httpCode === 429) $kind = 'quota';
            elseif ($useGoogleSearch && ($httpCode === 400 || strpos($apiMessage, 'google_search') !== false || strpos($apiMessage, 'grounding') !== false)) $kind = 'grounding_unavailable';
            return ['ok' => false, 'error' => $kind, 'message' => 'Yêu cầu AI chưa thực hiện được.', 'http_code' => $httpCode];
        }

        $answer = '';
        $sources = [];
        $searchQueries = [];
        foreach (($decoded['steps'] ?? []) as $step) {
            if (($step['type'] ?? '') === 'google_search_call') {
                foreach (($step['arguments']['queries'] ?? []) as $query) {
                    if (is_string($query) && $query !== '') $searchQueries[] = $query;
                }
            }
            if (($step['type'] ?? '') !== 'model_output') continue;
            foreach (($step['content'] ?? []) as $block) {
                if (($block['type'] ?? '') !== 'text') continue;
                $answer .= (string)($block['text'] ?? '');
                foreach (($block['annotations'] ?? []) as $annotation) {
                    if (($annotation['type'] ?? '') !== 'url_citation') continue;
                    $url = (string)($annotation['url'] ?? '');
                    if (strpos($url, 'https://') !== 0) continue;
                    $sources[$url] = [
                        'url' => $url,
                        'title' => trim((string)($annotation['title'] ?? parse_url($url, PHP_URL_HOST) ?? 'Nguồn tham khảo')),
                    ];
                }
            }
        }

        $answer = trim($answer);
        if ($answer === '') {
            return ['ok' => false, 'error' => 'empty_response', 'message' => 'AI chưa trả về nội dung tư vấn.'];
        }

        return [
            'ok' => true,
            'answer' => $answer,
            'sources' => array_values($sources),
            'search_queries' => array_values(array_unique($searchQueries)),
            'grounded' => !empty($searchQueries) || !empty($sources),
        ];
    }
}
