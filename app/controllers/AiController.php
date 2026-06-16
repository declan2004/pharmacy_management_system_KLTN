<?php
require_once __DIR__ . '/../../config/config.php';

class AiController extends Controller {
    private $apiKey = GEMINI_API_KEY; 
    private $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";

    // Core function to call Gemini API
    private function callGemini($prompt) {
        $url = $this->endpoint . "?key=" . $this->apiKey;
        $data = [
            "contents" => [
                ["parts" => [["text" => $prompt]]]
            ],
            // Force AI to structure the return data 100% as JSON
            "generationConfig" => [
                "response_mime_type" => "application/json"
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        // Bypass SSL for localhost (Laragon/XAMPP)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return json_encode(['error' => "cURL Error: " . $err]);
        }

        $result = json_decode($response, true);
        
        // Catch errors from Google API (e.g., Invalid API Key, Quota Exceeded)
        if (isset($result['error'])) {
            return json_encode(['error' => "Google API Error: " . $result['error']['message']]);
        }

        // Extract the generated text
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? "";
        return $text;
    }

    // Function to auto-fill medicine information (Master Data)
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

            // Send request to Google
            $response = $this->callGemini($prompt);
            
            // 1. Check for API errors
            $checkError = json_decode($response, true);
            if (isset($checkError['error'])) {
                echo json_encode(['success' => false, 'message' => $checkError['error']]);
                return;
            }

            // 2. Use Regular Expression (Regex) to safely extract the JSON object
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
                'message' => 'Failed to parse AI response. Please check F12 (Network tab) for details.',
                'raw_response' => $response
            ]);
        }
    }

    // Function to handle Copilot Chatbot at the POS screen (Context-Aware)
    public function chatCopilot() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userMessage = $_POST['message'] ?? '';
            
            if (empty(trim($userMessage))) {
                echo json_encode(['success' => false, 'message' => 'Please enter a message.']);
                return;
            }

            // Retrieve real-time inventory context from the Database
            $medicineModel = $this->model('Medicine');
            $availableMedicines = $medicineModel->getAvailableMedicinesList();

            // Prompt Engineering: 
            $prompt = "You are a Senior Clinical Pharmacist advising a POS staff member. 
                       CRITICAL INVENTORY CONSTRAINT: The pharmacy CURRENTLY ONLY HAS the following medicines in stock: [{$availableMedicines}]. 
                       When recommending medications or alternatives, YOU MUST STRICTLY ONLY RECOMMEND medicines from this available list. Do not invent or suggest medicines that are out of stock. If a condition requires a medicine not on the list, advise the patient to consult a doctor.
                       
                       User query: '{$userMessage}'
                       
                       STRICT CONSTRAINTS:
                       1. LANGUAGE MATCHING: You MUST detect the language of the 'User query' and respond in that EXACT SAME LANGUAGE.
                       2. DIRECT ANSWERS ONLY. No greetings, no polite fillers, no explanations of basic concepts.
                       3. LENGTH LIMIT: Maximum 3 short bullet points or 2 sentences. Keep it under 50 words.
                       4. Focus strictly on actionable advice: drug interactions, safe alternatives (from the available list), or specific dosages.
                       5. Use <b> tags for critical warnings (pregnancy, children, severe interactions).
                       6. Return EXACTLY a valid JSON string (no markdown, no ```json):
                       {
                           \"reply\": \"Use <ul>, <li>, <b> HTML tags. Extremely concise.\"
                       }";

            // Reuse the callGemini function
            $response = $this->callGemini($prompt);
            
            // Check for API errors
            $checkError = json_decode($response, true);
            if (isset($checkError['error'])) {
                echo json_encode(['success' => false, 'message' => 'API Error: ' . $checkError['error']]);
                return;
            }

            // Use Regex to safely extract the JSON object
            preg_match('/\{.*\}/s', $response, $matches);
            
            if (!empty($matches)) {
                $jsonString = $matches[0];
                $data = json_decode($jsonString, true);
                
                if ($data && isset($data['reply'])) {
                    echo json_encode(['success' => true, 'reply' => $data['reply']]);
                    return;
                }
            }

            echo json_encode(['success' => false, 'message' => 'AI system is busy or returned an invalid format. Please try again.']);
        }
    }
}