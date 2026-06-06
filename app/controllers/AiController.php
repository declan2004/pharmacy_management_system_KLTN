<?php
require_once __DIR__ . '/../../config/config.php';
class AiController extends Controller {
    private $apiKey = GEMINI_API_KEY; 
    private $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";
    // Hàm gọi API lõi
    private function callGemini($prompt) {
        $url = $this->endpoint . "?key=" . $this->apiKey;
        $data = [
            "contents" => [
                ["parts" => [["text" => $prompt]]]
            ],
            // Ép AI cấu trúc dữ liệu trả về 100% là JSON
            "generationConfig" => [
                "response_mime_type" => "application/json"
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        // Bỏ qua SSL cho localhost (Laragon)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return json_encode(['error' => "cURL Error: " . $err]);
        }

        $result = json_decode($response, true);
        
        // Bắt lỗi từ Google API (Ví dụ: Sai API Key, hết hạn mức)
        if (isset($result['error'])) {
            return json_encode(['error' => "Google API Error: " . $result['error']['message']]);
        }

        // Lấy đoạn text AI sinh ra
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? "";
        return $text;
    }

    // Hàm tự động điền thông tin thuốc 
    public function generateMedicineInfo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $medicineName = $_POST['medicine_name'] ?? '';
            
            if (empty($medicineName)) {
                echo json_encode(['success' => false, 'message' => 'Please enter Medicine Name first.']);
                return;
            }

            $prompt = "Act as a clinical pharmacist. Provide standard medical information for the medicine: '{$medicineName}'. 
                       The response MUST be entirely in English. 
                       Return ONLY a valid JSON object using this exact structure:
                       {
                           \"active_ingredient\": \"Main compound(s) only\",
                           \"concentration\": \"500mg, 10mg/ml\",
                           \"unit\": \"Suggest the most common dispensing unit (e.g., Box, Bottle, Tube, Blister, Pill)\",
                           \"medicine_type\": \"Determine if it requires a prescription. Return EXACTLY 'OTC' or 'ETC'\",
                           \"description\": \"Write a clear, concise paragraph including: Usage, Dosage, Side effects, and Contraindications.\"
                       }";

            // Bắn Request lên Google
            $response = $this->callGemini($prompt);
            
            // 1. Kiểm tra xem có lỗi API trả về không
            $checkError = json_decode($response, true);
            if (isset($checkError['error'])) {
                echo json_encode(['success' => false, 'message' => $checkError['error']]);
                return;
            }

            // 2. Dùng Biểu thức chính quy (Regex) quét tìm đúng cặp ngoặc nhọn của JSON 
            preg_match('/\{.*\}/s', $response, $matches);
            
            if (!empty($matches)) {
                $jsonString = $matches[0];
                $data = json_decode($jsonString, true);
                
                if ($data) {
                    echo json_encode(['success' => true, 'data' => $data]);
                    return;
                }
            }

            echo json_encode([
                'success' => false, 
                'message' => 'Failed to parse AI response. Vui lòng bật F12 (Network) để xem chi tiết chuỗi AI trả về.',
                'raw_response' => $response
            ]);
        }
    }

    // Hàm xử lý Chatbot Copilot tại màn hình POS 
    public function chatCopilot() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userMessage = $_POST['message'] ?? '';
            
            if (empty(trim($userMessage))) {
                echo json_encode(['success' => false, 'message' => 'Please enter a message.']);
                return;
            }

            // Kỹ nghệ Câu lệnh (Prompt Engineering) thiết lập nhân cách cho AI bằng Tiếng Anh
            $prompt = "You are a Senior Clinical Pharmacist advising a POS staff member. 
                       User query: '{$userMessage}'
                       
                       STRICT CONSTRAINTS:
                       1. DIRECT ANSWERS ONLY. No greetings, no polite fillers, no explanations of basic concepts.
                       2. LENGTH LIMIT: Maximum 3 short bullet points or 2 sentences. Keep it under 50 words.
                       3. Focus strictly on actionable advice: drug interactions, safe alternatives, or specific dosages.
                       4. Use <b> tags for critical warnings (pregnancy, children, severe interactions).
                       5. Return EXACTLY a valid JSON string (no markdown, no ```json):
                       {
                           \"reply\": \"Use <ul>, <li>, <b> HTML tags. Extremely concise.\"
                       }";

            // Tận dụng lại hàm callGemini
            $response = $this->callGemini($prompt);
            
            // Bắt lỗi từ Google API
            $checkError = json_decode($response, true);
            if (isset($checkError['error'])) {
                echo json_encode(['success' => false, 'message' => 'API Error: ' . $checkError['error']]);
                return;
            }

            // Dùng Regex bóc tách JSON an toàn
            preg_match('/\{.*\}/s', $response, $matches);
            
            if (!empty($matches)) {
                $jsonString = $matches[0];
                $data = json_decode($jsonString, true);
                
                if ($data && isset($data['reply'])) {
                    echo json_encode(['success' => true, 'reply' => $data['reply']]);
                    return;
                }
            }

            echo json_encode(['success' => false, 'message' => 'AI system is busy or returned invalid format. Please try again.']);
        }
    }
}