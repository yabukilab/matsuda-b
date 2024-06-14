<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>サブページ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            background-color: #ffffff;
            border-top: 10px solid #0078d7;
            box-sizing: border-box;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        header {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 2px solid #ccc;
            background-color: #f5f5f5;
        }
        .circle {
            width: 100px;
            height: 100px;
            background-color: #4a90e2;
            border-radius: 50%;
            margin-right: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .circle i {
            font-size: 3em;
            color: white;
        }
        header h1 {
            font-size: 2em;
            margin: 0 20px;
            flex-grow: 1;
        }
        .stars {
            display: flex;
            align-items: center;
            color: #4a90e2;
        }
        .stars i {
            font-size: 2em;
            margin: 0 5px;
            cursor: pointer;
        }
        .stars .selected {
            color: gold;
        }
        .pen-icon {
            margin-left: 20px;
            cursor: pointer;
        }
        .pen-icon i {
            font-size: 1.5em;
            color: #4a90e2;
        }
        main {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            padding: 0 20px;
        }
        .item {
            width: 30%;
            background-color: #4a90e2;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            margin-bottom: 20px;
            position: relative;
        }
        .item h3,
        .item p {
            cursor: pointer;
        }
        .item h3 {
            margin: 10px 0;
        }
        .item p {
            margin: 10px 0 0;
            padding: 10px 0;
            background-color: #316bbf;
            border-radius: 5px;
        }
        .delete-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            color: #fff;
            font-size: 1.2em;
        }
        .theme-switch {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: transparent;
            border: none;
            cursor: pointer;
            font-size: 1.5em;
            color: #4a90e2;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <button class="theme-switch" id="theme-switch">
        <i class="fas fa-adjust"></i>
    </button>
    <div class="container">
        <header>
            <div class="circle">
                <i class="fas fa-gamepad"></i>
            </div>
            <h1 id="title">タイトル</h1>
            <div class="stars" id="stars">
                <i class="fas fa-star" data-value="1"></i>
                <i class="fas fa-star" data-value="2"></i>
                <i class="fas fa-star" data-value="3"></i>
                <i class="fas fa-star" data-value="4"></i>
                <i class="fas fa-star" data-value="5"></i>
            </div>
            <div class="pen-icon" id="edit-icon">
                <i class="fas fa-pen"></i>
            </div>
        </header>
        <main>
            <div class="item">
                <h3 class="editable">項目1</h3>
                <p class="editable">掲示板</p>
                <i class="fas fa-trash-alt delete-icon"></i>
            </div>
            <div class="item">
                <h3 class="editable">項目2</h3>
                <p class="editable">掲示板</p>
                <i class="fas fa-trash-alt delete-icon"></i>
            </div>
            <div class="item">
                <h3 class="editable">項目3</h3>
                <p class="editable">掲示板</p>
                <i class="fas fa-trash-alt delete-icon"></i>
            </div>
        </main>
        <button id="add-item">項目を追加する</button>
    </div>

    <script>
        function makeEditable(element) {
            var currentText = element.textContent;
            var input = document.createElement('input');
            input.type = 'text';
            input.value = currentText;
            input.style.fontSize = '1em';
            input.style.width = '100%';
            input.style.boxSizing = 'border-box';

            element.replaceWith(input);
            input.focus();

            input.addEventListener('blur', function() {
                var newText = input.value;
                element.textContent = newText;
                input.replaceWith(element);
            });

            input.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    input.blur();
                }
            });
        }

        document.getElementById('edit-icon').addEventListener('click', function() {
            var title = document.getElementById('title');
            makeEditable(title);
        });

        document.querySelectorAll('.editable').forEach(function(element) {
            element.addEventListener('click', function() {
                makeEditable(element);
            });
        });

        document.querySelectorAll('.delete-icon').forEach(function(icon) {
            icon.addEventListener('click', function() {
                var item = this.closest('.item');
                item.remove();
            });
        });

        document.querySelectorAll('.stars i').forEach(function(star) {
            star.addEventListener('click', function() {
                var value = this.getAttribute('data-value');
                document.querySelectorAll('.stars i').forEach(function(s, index) {
                    if (index < value) {
                        s.classList.add('selected');
                    } else {
                        s.classList.remove('selected');
                    }
                });
            });
        });

        document.getElementById('add-item').addEventListener('click', function() {
            var newItem = document.createElement('div');
            newItem.classList.add('item');
            newItem.innerHTML = `
                <h3 class="editable">新しい項目</h3>
                <p class="editable">掲示板</p>
                <i class="fas fa-trash-alt delete-icon"></i>
            `;
            document.querySelector('main').appendChild(newItem);

            // Make new elements editable
            newItem.querySelector('h3').addEventListener('click', function() {
                makeEditable(this);
            });
            newItem.querySelector('p').addEventListener('click', function() {
                makeEditable(this);
            });

            // Add delete functionality
            newItem.querySelector('.delete-icon').addEventListener('click', function() {
                newItem.remove();
            });
        });

        // Dark mode toggle functionality (removed for light mode only)
    </script>
</body>
</html>
