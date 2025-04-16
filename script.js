
        const form = document.getElementById('search-form');
        const input = document.getElementById('search-input');
        const resultsContainer = document.getElementById('search-results'); //hajar:notice que j'ai changer ça de classe à id
        const favoritesBtn = document.getElementById('add-fav-btn');
        const favoritesContainer = document.getElementById('favorites-list');
        const apiKey = 'AIzaSyA4jauCd-3cxIwx3HzF4QfzCzUucCR2FBI';

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const query = input.value.trim();

            if (!query) {
                alert("Veuillez entrer un titre de livre.");
                return;
            }

            fetch(`https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(query)}&key=${apiKey}`)
                .then(res => res.json())
                .then(data => {
                    console.log("API Response:", data);
 
                    if (data.items) {
                        displayBooks(data.items);
                    } else {
                        resultsContainer.innerHTML = "<p>Aucun livre trouvé.</p>";
                    }
                })
                .catch(err => console.error("Erreur lors de la récupération des livres :", err));
        });

        function displayBooks(books) {
            resultsContainer.innerHTML = "";

            books.forEach(book => {
                const bookCard = document.createElement("div");
                bookCard.classList.add("book-card");

                const title = document.createElement("h3");
                title.textContent = book.volumeInfo.title || "Titre inconnu";

                const author = document.createElement("p");
                author.textContent = `Auteur(s) : ${book.volumeInfo.authors ? book.volumeInfo.authors.join(", ") : "Inconnu"}`;

                const img = document.createElement("img");
                img.src = book.volumeInfo.imageLinks ? book.volumeInfo.imageLinks.thumbnail : 'https://via.placeholder.com/128x192';
                img.alt = "Couverture du livre";

                const favBtn = document.createElement("button");
                favBtn.textContent = isBookInFavs(book.id) ? "Retirer des favoris" : "Ajouter aux favoris";
                favBtn.addEventListener("click", () => toggleFav(book, favBtn));

                bookCard.appendChild(title);
                bookCard.appendChild(author);
                bookCard.appendChild(img);
                bookCard.appendChild(favBtn);

                resultsContainer.appendChild(bookCard);
            });
        }

        function isBookInFavs(bookId) {
            const favs = JSON.parse(localStorage.getItem("favs")) || [];
            return favs.some(fav => fav.id === bookId);
        }

        function toggleFav(book, btn) {
            let favs = JSON.parse(localStorage.getItem("favs")) || [];

            if (isBookInFavs(book.id)) {
                favs = favs.filter(fav => fav.id !== book.id);
                btn.textContent = "Ajouter aux favoris";
                console.log(`Retiré des favoris : ${book.volumeInfo.title}`);
            } else {
                const bookData = {
                    id: book.id,
                    title: book.volumeInfo.title || "Titre inconnu",
                    img: book.volumeInfo.imageLinks ? book.volumeInfo.imageLinks.thumbnail : 'https://via.placeholder.com/128x192'
                };
                favs.push(bookData);
                btn.textContent = "Retirer des favoris";
                console.log(`Ajouté aux favoris : ${book.volumeInfo.title}`);
            }

            localStorage.setItem("favs", JSON.stringify(favs));
        }

        favoritesBtn.addEventListener("click", () => {
            console.log("Affichage des favoris");
            favoritesContainer.innerHTML = "";
            const favs = JSON.parse(localStorage.getItem("favs")) || [];

            if (favs.length > 0) {
                favs.forEach(book => {
                    const bookCard = document.createElement('div');
                    bookCard.classList.add('book-card');

                    const title = document.createElement('h2');
                    title.textContent = book.title;

                    const img = document.createElement('img');
                    img.src = book.img;
                    img.alt = book.title;

                    const removeBtn = document.createElement('button');
                    removeBtn.textContent = "Retirer des favoris";
                    removeBtn.addEventListener("click", () => {
                        toggleFav(book, removeBtn);
                        bookCard.remove();
                    });

                    bookCard.appendChild(title);
                    bookCard.appendChild(img);
                    bookCard.appendChild(removeBtn);
                    favoritesContainer.appendChild(bookCard);
                });
            } else {
                favoritesContainer.innerHTML = "<p>Aucun favori pour le moment.</p>";
            }
        });
        
