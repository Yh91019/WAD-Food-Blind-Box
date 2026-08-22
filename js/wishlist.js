/* ===================== RESTAURANT LIST ===================== */

const restaurants = [

    "McDonald's",

    "KFC",

    "Pizza Hut",

    "Subway",

    "Domino's",

    "Sushi King"

];

/* ===================== SEARCH SUGGESTIONS ===================== */

function showSuggestions(){

    let input =
        document.getElementById("wishlist-search-input")
        .value
        .toLowerCase();

    let suggestionBox =
        document.getElementById("suggestion-box");

    suggestionBox.innerHTML = "";

    if(input === ""){

        suggestionBox.style.display = "none";

        searchWishlist("");

        return;

    }

    let found = false;

    restaurants.forEach(function(name){

        if(name.toLowerCase().startsWith(input)){

            found = true;

            let div = document.createElement("div");

            div.className = "suggestion-item";

            div.innerHTML = name;

            div.onclick = function(){

                document.getElementById("wishlist-search-input").value = name;

                suggestionBox.style.display = "none";

                searchWishlist(name);

            };

            suggestionBox.appendChild(div);

        }

    });

    suggestionBox.style.display = found ? "block" : "none";

    searchWishlist(input);

}

/* ===================== SEARCH CARDS ===================== */

function searchWishlist(keyword = ""){

    let filter;

    if(keyword === ""){

        filter = document.getElementById("wishlist-search-input")
        .value
        .toUpperCase();

    }else{

        filter = keyword.toUpperCase();

    }

    let cards =
        document.querySelectorAll(".wishlist-card");

    cards.forEach(function(card){

        let restaurant =
            card.querySelector(".wishlist-restaurant")
            .innerText
            .toUpperCase();

        let food =
            card.querySelector(".wishlist-food-name")
            .innerText
            .toUpperCase();

        if(

            restaurant.includes(filter)

            ||

            food.includes(filter)

        ){

            card.style.display = "block";

        }else{

            card.style.display = "none";

        }

    });

}

/* ===================== REMOVE ===================== */

let removeButtons =
    document.querySelectorAll(".wishlist-remove-btn");

removeButtons.forEach(function(button){

    button.addEventListener("click", function(){

        if(confirm("Remove this item from wishlist?")){

            this.closest(".wishlist-card").remove();

        }

    });

});

/* ===================== ADD TO CART ===================== */

/* No click handler needed here — the button is a real form submit
   button, and the server (pages/wishlist.php) decides the outcome:
   it redirects to the cart on success, or reloads this page with a
   message if the restaurant is closed. A JS alert here would have
   claimed success before the server had actually checked. */

/* ===================== CLICK OUTSIDE ===================== */

document.addEventListener("click", function(e){

    let input =
        document.getElementById("wishlist-search-input");

    let suggestion =
        document.getElementById("suggestion-box");

    if(

        e.target !== input

        &&

        !suggestion.contains(e.target)

    ){

        suggestion.style.display = "none";

    }

});