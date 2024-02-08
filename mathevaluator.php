<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Expression Evaluator</title>
</head>
<body>
    <h1>Math Expression Evaluator</h1>

    <?php
    function evaluateExpression($expression) {
        $expression = str_replace(' ', '', $expression);

        $steps = [];

        while (preg_match('/\(([^\(\)]+)\)/', $expression, $matches)) {
            $result = evaluateExpression($matches[1]);
            $expression = str_replace('(' . $matches[1] . ')', $result['result'], $expression);
            $steps[] = $expression;
        }

        while (preg_match('/(\d+)([\/\*])(\d+)/', $expression, $matches)) {
            $operand1 = $matches[1];
            $operator = $matches[2];
            $operand2 = $matches[3];

            $result = ($operator == '*') ? $operand1 * $operand2 : $operand1 / $operand2;
            $expression = preg_replace('/\b' . preg_quote($matches[0], '/') . '\b/', $result, $expression, 1);
            $steps[] = $expression;
        }

        while (preg_match('/(\d+)([+\-])(\d+)/', $expression, $matches)) {
            $operand1 = $matches[1];
            $operator = $matches[2];
            $operand2 = $matches[3];

            $result = ($operator == '+') ? $operand1 + $operand2 : $operand1 - $operand2;
            $expression = preg_replace('/\b' . preg_quote($matches[0], '/') . '\b/', $result, $expression, 1);
            $steps[] = $expression;
        }

        return ['result' => $expression, 'steps' => $steps];
    }

    $expression = '';
    $output = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $expression = $_POST['expression'];
        $output = evaluateExpression($expression);
    }
    ?>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="expression">Enter your mathematical expression:</label>
        <input type="text" name="expression" value="<?php echo htmlspecialchars($expression); ?>" required>
        <button type="submit">Evaluate</button>
    </form>

    <?php if (!empty($output)): ?>
        <h2>Steps:</h2>
        <?php foreach ($output['steps'] as $step): ?>
            <p><?php echo $step; ?></p>
        <?php endforeach; ?>

        <h2>Result:</h2>
        <p><?php echo $output['result']; ?></p>
    <?php endif; ?>
</body>
</html>
