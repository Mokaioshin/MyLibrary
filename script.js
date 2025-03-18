const form = document.getElementById('search-form');
const input = document.getElementById('search-input');
const resultsContainer = document.querySelector('.search-results');
const favoritesBtn = document.querySelector("#add-fav-btn");
let apiKey = 'AIzaSyDVRJW3-7uMKJYWNRt_LjTDo4n6dXpmkqk';
const submit = document.getElementById('submit');
console.log('Message')

// 🔹 Empêcher la page de se recharger lors de la recherche
submit.addEventListener('click', function (e) {
    const query = input.value.trim();
    console.log('click')

    if (query) {
        fetch(`https://www.googleapis.com/books/v1/volumes?q=${query}&key=${apiKey}`)
            .then(res => res.json())
            .then(data => displayBooks(data.items))
            .catch(err => console.log("Erreur lors de la récupération des livres : ", err));
    } else {
        alert("Veuillez entrer un titre de livre.");
    }
});

// 🔹 Fonction pour afficher les résultats de recherche
function displayBooks(books) {
    resultsContainer.innerHTML = ""; // Réinitialisation de l'affichage

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

            // 🔹 Création du bouton Favori
            const favBtn = document.createElement('button');
            favBtn.textContent = isBookInFavs(book.id) ? "Retirer des favoris" : "Ajouter aux favoris";
            favBtn.addEventListener("click", () => toggleFav(book, favBtn));
            bookCard.appendChild(favBtn);

            resultsContainer.appendChild(bookCard);
        });
    } else {
        resultsContainer.innerHTML = "<p>Aucun livre trouvé.</p>";
    }
    console.log(bookCard)
}

// 🔹 Vérifier si un livre est déjà en favoris
function isBookInFavs(bookId) {
    const favs = JSON.parse(localStorage.getItem("favs")) || [];
    return favs.some(fav => fav.id === bookId);
}

// 🔹 Ajouter ou retirer un livre des favoris
function toggleFav(book, btn) {
    let favs = JSON.parse(localStorage.getItem("favs")) || [];

    if (isBookInFavs(book.id)) {
        favs = favs.filter(fav => fav.id !== book.id);
        btn.textContent = "Ajouter aux favoris";
        console.log(`Retiré des favoris : ${book.title}`);
    } else {
        const bookData = {
            id: book.id,
            title: book.volumeInfo.title || "Titre inconnu",
            img: book.volumeInfo.imageLinks ? book.volumeInfo.imageLinks.thumbnail : 'https://via.placeholder.com/128x192'
        };
        favs.push(bookData);
        btn.textContent = "Retirer des favoris";
        console.log(`Ajouté aux favoris : ${book.title}`);
    }

    localStorage.setItem("favs", JSON.stringify(favs));
}

// 🔹 Affichage des favoris
favoritesBtn.addEventListener("click", () => {
    console.log(" Affichage des favoris");

    resultsContainer.innerHTML = "";
    const favs = JSON.parse(localStorage.getItem("favs")) || [];

    if (favs.length > 0) {
        favs.forEach(book => {
            const bookCard = document.createElement('div');
            bookCard.classList.add('book-card');

            const title = document.createElement('h2');
            title.textContent = book.title;
            bookCard.appendChild(title);

            const img = document.createElement('img');
            img.src = book.img;
            img.alt = book.title;
            bookCard.appendChild(img);

            // 🔹 Bouton de suppression des favoris
            const removeBtn = document.createElement('button');
            removeBtn.textContent = "Retirer des favoris";
            removeBtn.addEventListener("click", () => {
                toggleFav(book, removeBtn);
                bookCard.remove();
            });
            bookCard.appendChild(removeBtn);

            resultsContainer.appendChild(bookCard);
        });
    } else {
        resultsContainer.innerHTML = "<p>Aucun favori pour le moment.</p>";
    }
});
