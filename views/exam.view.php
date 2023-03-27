<script>
    const questions = JSON.parse(`<?= $questions; ?>`).questions;
    console.log(questions)
    let current = 0;
    const questionsCount = questions.length;
</script>
<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-center row">
        <div class="col-md-10 col-lg-10">
            <div class="border rounded-3">
                <div class="question p-3 border-bottom">
                    <div class="d-flex flex-row justify-content-between align-items-center mcq">
                        <h4><?= $exam ?></h4><span id="questionCounter">(1 z 1)</span></div>
                </div>
                <div id="questionData" class="question p-3 border-bottom">
                </div>
                <div class="d-flex flex-row justify-content-between align-items-center p-3" id="questionButtons"><button class="btn btn-primary d-flex align-items-center btn-danger" type="button">Cofnij</button><button class="btn btn-primary border-success align-items-center btn-success" type="button">Dalej<i class="fa fa-angle-right ml-2"></i></button></div>
            </div>
        </div>
    </div>
</div>
<script>
    const questionCounter = document.getElementById('questionCounter');
    const questionData = document.getElementById('questionData');
    const questionButtons = document.getElementById('questionButtons');
    const nextButton = document.querySelector('.btn-success');
    const previousButton = document.querySelector('.btn-danger');
    let answers = [];
    answers.length = questionsCount;
    answers.fill(null);

    document.addEventListener('DOMContentLoaded', () => {
        updateCounter();
        updateQuestion();
    })

    const updateAnswer = () => {
        answers[current] = document.querySelector('input[name="answer"]:checked')?.value;
    }

    const updateQuestion = () => {
        questionData.innerHTML = '';
        const question = questions[current];
        const questionDiv = document.createElement('div');
        questionDiv.classList.add('d-flex', 'flex-row', 'question-title');
        const questionTitle = document.createElement('h5');
        questionTitle.innerHTML = question.question;
        questionDiv.appendChild(questionTitle);
        questionData.appendChild(questionDiv);
        const answersDiv = document.createElement('div');
        answersDiv.classList.add('d-flex', 'flex-column', 'ml-4');
        for(let i = 0; i < question.answers.length; i++) {
            const answer = question.answers[i];
            const answerDiv = document.createElement('div');
            answerDiv.classList.add('form-check');
            const answerInput = document.createElement('input');
            answerInput.classList.add('form-check-input');
            answerInput.type = 'radio';
            answerInput.name = 'answer';
            answerInput.id = `answer${i}`;
            answerInput.value = answer;
            answerInput.addEventListener('change', () => {
                answers[current] = answer;
            });
            answerDiv.appendChild(answerInput);
            const answerLabel = document.createElement('label');
            answerLabel.classList.add('form-check-label');
            answerLabel.htmlFor = `answer${i}`;
            answerLabel.innerHTML = answer;
            answerDiv.appendChild(answerLabel);
            answersDiv.appendChild(answerDiv);
        }
        questionData.appendChild(answersDiv);
        if(answers[current]) {
            document.querySelector(`input[value="${answers[current]}"]`).checked = true;
        }
    }

    const calculateScore = () => {
        let score = 0;
        for(let i = 0; i < questionsCount; i++) {
            if(answers[i] === questions[i].answers[questions[i].correctAnswer]) score++;
        }
        questionData.innerHTML = '';
        const scoreDiv = document.createElement('div');
        scoreDiv.classList.add('d-flex', 'flex-column', 'justify-content-center', 'align-items-center');
        const scoreTitle = document.createElement('h3');
        scoreTitle.innerText = 'Wynik';
        scoreDiv.appendChild(scoreTitle);
        const scoreValue = document.createElement('h1');
        scoreValue.innerText = `${score}/${questionsCount}`;
        scoreDiv.appendChild(scoreValue);
        questionData.appendChild(scoreDiv);
        for(let j = 0; j < questionsCount; j++){
            const question = questions[j];
            const questionDiv = document.createElement('div');
            questionDiv.classList.add('mt-2', 'pt-3', 'border-top', 'd-flex', 'flex-row', 'question-title');
            const questionTitle = document.createElement('h5');
            questionTitle.innerHTML = question.question;
            questionDiv.appendChild(questionTitle);
            questionData.appendChild(questionDiv);
            const answersDiv = document.createElement('div');
            answersDiv.classList.add('d-flex', 'flex-column', 'ml-4');
            for(let i = 0; i < question.answers.length; i++) {
                const answer = question.answers[i];
                const answerDiv = document.createElement('div');
                answerDiv.classList.add('form-check');
                const answerInput = document.createElement('input');
                answerInput.classList.add('form-check-input');
                answerInput.type = 'radio';
                answerInput.name = 'answer'+j;
                answerInput.id = `answer${i}`;
                answerInput.value = answer;
                answerInput.disabled = true;
                answerDiv.appendChild(answerInput);
                const answerLabel = document.createElement('label');
                answerLabel.classList.add('form-check-label');
                answerLabel.htmlFor = `answer${i}`;
                answerLabel.innerHTML = answer;
                answerDiv.appendChild(answerLabel);
                answersDiv.appendChild(answerDiv);
            }
            questionData.appendChild(answersDiv);
            if(answers[j]) {
                document.querySelector(`input[value="${answers[j]}"][name="answer${j}"]`).checked = true;
            }
            if(answers[j] !== questions[j].answers[questions[j].correctAnswer])
                document.querySelector(`input[value="${answers[j]}"][name="answer${j}"]`).parentElement.classList.add('text-danger')
            document.querySelector(`input[value="${questions[j].answers[questions[j].correctAnswer]}"][name="answer${j}"]`).parentElement.classList.add('text-success')
        }
        questionButtons.innerHTML = '';
        const backButton = document.createElement('button');
        backButton.classList.add('btn', 'btn-primary', 'd-flex', 'align-items-center', 'btn-danger');
        backButton.type = 'button';
        backButton.innerText = 'Powrót';
        const formData = new FormData();
        formData.append('answers', JSON.stringify(answers));
        formData.append('result', `${score}/${questionsCount}`);
        fetch(`/exam/<?= $examId ?>/finish`, {
            method: 'POST',
            body: formData
        })
        backButton.addEventListener('click', () => {
            window.location.href = '/';
        });
        questionButtons.appendChild(backButton);
        return score;
    }

    const updateCounter = () => {
        questionCounter.innerText = `(${current + 1} z ${questionsCount})`;
    }

    const next = () => {
        updateAnswer();
        if(nextButton.innerText === 'Zakończ') {
            calculateScore();
            return;
        }
        if(current == questionsCount - 1) return;
        current++;
        updateCounter();
        updateQuestion();
        if(current == questionsCount - 1) nextButton.innerText = 'Zakończ';
    }

    const previous = () => {
        updateAnswer();
        if(current == 0) window.location.href = '/';
        current--;
        updateCounter();
        updateQuestion();
        if(current != questionsCount - 1) nextButton.innerText = 'Dalej';
    }

    nextButton.addEventListener('click', next);
    previousButton.addEventListener('click', previous);
</script>