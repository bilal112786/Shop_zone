<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Chrome Dino Game</title>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #f7f7f7;
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100vh;
        overflow: hidden;
    }

    h1 {
        margin-top: 20px;
    }

    .game {
        position: relative;
        width: 800px;
        height: 200px;
        background: #fff;
        border: 2px solid #333;
        overflow: hidden;
        margin-top: 20px;
    }

    .dino {
        position: absolute;
        bottom: 0;
        left: 50px;
        width: 44px;
        height: 47px;
        background: url('https://i.imgur.com/0X2ZQ9G.png') no-repeat;
        background-size: cover;
    }

    .cactus {
        position: absolute;
        bottom: 0;
        width: 25px;
        height: 50px;
        background: url('https://i.imgur.com/7rVbEJm.png') no-repeat;
        background-size: cover;
        right: 0;
    }

    .score {
        margin-top: 20px;
        font-size: 20px;
    }
</style>
</head>
<body>
<h1>Chrome Dino Game</h1>
<div class="game" id="game">
    <div class="dino" id="dino"></div>
</div>
<div class="score" id="score">Score: 0</div>

<script>
const dino = document.getElementById('dino');
const game = document.getElementById('game');
const scoreEl = document.getElementById('score');

let isJumping = false;
let position = 0;
let score = 0;

// Jump logic
function jump() {
    if (isJumping) return;
    isJumping = true;
    let upInterval = setInterval(() => {
        if (position >= 120) {
            clearInterval(upInterval);
            // descend
            let downInterval = setInterval(() => {
                if (position <= 0) {
                    clearInterval(downInterval);
                    isJumping = false;
                }
                position -= 5;
                dino.style.bottom = position + 'px';
            }, 20);
        }
        position += 5;
        dino.style.bottom = position + 'px';
    }, 20);
}

document.addEventListener('keydown', (e) => {
    if (e.code === "Space") jump();
});

// Cactus creation
function createCactus() {
    const cactus = document.createElement('div');
    cactus.classList.add('cactus');
    game.appendChild(cactus);
    let cactusPosition = 800;

    const timerId = setInterval(() => {
        if (cactusPosition < -25) {
            clearInterval(timerId);
            game.removeChild(cactus);
            score += 10;
            scoreEl.innerText = "Score: " + score;
        }

        // Collision detection
        if (
            cactusPosition > 50 && cactusPosition < 94 && position < 50
        ) {
            alert("Game Over! Your score: " + score);
            location.reload();
        }

        cactusPosition -= 10;
        cactus.style.left = cactusPosition + 'px';
    }, 20);

    setTimeout(createCactus, Math.random() * 4000 + 1000);
}

createCactus();
</script>
</body>
</html>
