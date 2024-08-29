<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Function to check if a number is prime
function isPrime($num) {
    if ($num <= 1) return false;
    if ($num <= 3) return true;
    if ($num % 2 == 0 || $num % 3 == 0) return false;
    $i = 5;
    while ($i * $i <= $num) {
        if ($num % $i == 0 || $num % ($i + 2) == 0) return false;
        $i += 6;
    }
    return true;
}

// Function to find the least primitive root of a prime number p
function primitiveRoot($p) {
    $phi = $p - 1; // Euler's totient function
    $factors = primeFactors($phi);
    for ($g = 2; $g <= $p; $g++) {
        $isPrimitive = true;
        foreach ($factors as $factor) {
            if (bcpowmod($g, $phi / $factor, $p) == 1) {
                $isPrimitive = false;
                break;
            }
        }
        if ($isPrimitive) return $g;
    }
    return null;
}

// Function to find prime factors of a number
function primeFactors($num) {
    $factors = [];
    for ($i = 2; $i <= $num / $i; $i++) {
        while ($num % $i == 0) {
            $factors[] = $i;
            $num /= $i;
        }
    }
    if ($num > 1) $factors[] = $num;
    return array_unique($factors);
}

// Generate and store three pairs of p and g
$pg_pairs = [];
for ($i = 0; $i < 3; $i++) {
    do {
        $p = mt_rand(100, 150);
    } while (!isPrime($p));
    $g = primitiveRoot($p);
    $pg_pairs[] = ['p' => $p, 'g' => $g];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 600px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1, h3 {
            margin-top: 0;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 15px;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            border: none;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        #timer {
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Page</h1>
        <h3>Three pairs of p and g:</h3>
        <form id="pair_form" action="process.php" method="post">
            <ul>
                <?php foreach ($pg_pairs as $index => $pair): ?>
                    <li>
                        <label>
                            <input type="radio" name="pg_pair" value="<?php echo $index; ?>" onclick="updateSelectedValues('<?php echo $pair['p']; ?>', '<?php echo $pair['g']; ?>')">
                            Prime number (p): <?php echo $pair['p']; ?>, Primitive root (g): <?php echo $pair['g']; ?>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
            <input type="hidden" id="selected_p" name="selected_p">
            <input type="hidden" id="selected_g" name="selected_g">
        </form>
        <p id="timer"></p>
    </div>

    <script>
        function updateSelectedValues(p, g) {
            document.getElementById('selected_p').value = p;
            document.getElementById('selected_g').value = g;
        }

        window.onload = function() {
            var countDownDate = new Date().getTime() + 12000;
            var x = setInterval(function() {
                var now = new Date().getTime();
                var distance = countDownDate - now;
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById("timer").innerHTML = "Redirecting in " + seconds + "s";
                if (distance <= 0) {
                    clearInterval(x);
                    var selectedPair = document.querySelector('input[name="pg_pair"]:checked');
                    if (selectedPair) {
                        document.getElementById('pair_form').submit();
                    } else {
                        window.location.href = "welcome.php";
                    }
                }
            }, 1000);
        };
    </script>
</body>
</html>
