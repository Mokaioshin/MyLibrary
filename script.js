const form = document.getElementById('search-form');
const input = document.getElementById('search-input');
const resultsContainer = document.querySelector('.search-results');
let apiKey = 'AIzaSyDVRJW3-7uMKJYWNRt_LjTDo4n6dXpmkqk';

// Eviter que la page recharge à chaque recherche
form.addEventListener('submit', function (e) {
    e.preventDefault();
    const query = input.value.trim();

    if (query) {
        fetch(`https://www.googleapis.com/books/v1/volumes?q=${query}&key=${apiKey}`)
            .then(res => res.json())
            .then(data => displayBooks(data.items))
            .catch(err => console.log("Erreur lors de la récupération des livres : ", err));
    } else {
        alert("Veuillez entrer un titre de livre.");
    }
});

function displayBooks(books) {
    resultsContainer.innerHTML = "";  // Réinitialise les résultats à chaque recherche

    if (books && books.length > 0) {
        books.forEach(book => {
            const bookCard = document.createElement('div');
            bookCard.classList.add('book-card');

            const title = document.createElement('h2');
            title.textContent = book.volumeInfo.title || "Titre inconnu";
            bookCard.appendChild(title);

            const img = document.createElement('img');
            img.src = book.volumeInfo.imageLinks ? book.volumeInfo.imageLinks.thumbnail : 'https://via.placeholder.com/128x192';
            img.alt = book.volumeInfo.title;
            bookCard.appendChild(img);

        

            resultsContainer.appendChild(bookCard);
        });
    } else {
        resultsContainer.innerHTML = "<p>Aucun livre trouvé.</p>";
    }
}
