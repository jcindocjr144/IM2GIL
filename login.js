 const textElement = document.getElementById('text');
        const text = textElement.innerText;
        textElement.innerHTML = ''; // Clear the text

        // Create a span for each letter
        text.split('').forEach((letter, index) => {
            const span = document.createElement('span');
            span.innerText = letter;
            span.classList.add('waving-text');
            // Set a delay based on the letter's index
            span.style.animation = `wave 1s ease-in-out infinite`;
            span.style.animationDelay = `${index * 0.1}s`; // Adjust delay as needed
            textElement.appendChild(span); // Append to the text element
        });