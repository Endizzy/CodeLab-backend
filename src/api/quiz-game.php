<?php

$topic = $_GET['topic'] ?? null;
$action = $_GET['action'] ?? 'getQuestions';

$allowedTopics = ['js', 'css', 'html'];

if (!in_array($topic, $allowedTopics)) {
    echo json_encode(['error' => 'Invalid topic']);
    exit;
}

$data = json_decode(file_get_contents(__DIR__ . "/../quizgame-questions/$topic.json"), true);

if ($action === 'getQuestions') {
    $questions = array_map(function($q) {
        unset($q['correct']);
        return $q;
    }, $data);
    echo json_encode($questions);
    exit;
}

if ($action === 'checkAnswer') {
    $input = json_decode(file_get_contents('php://input'), true);
    $questionId = $input['questionId'] ?? null;
    $answerIndex = $input['answerIndex'] ?? null;

    foreach ($data as $q) {
        if ($q['id'] == $questionId) {
            echo json_encode([
                'isCorrect' => $q['correct'] === $answerIndex,
                'correctIndex' => $q['correct']
            ]);
            exit;
        }
    }
    echo json_encode(['error' => 'Question not found']);
    exit;
}
