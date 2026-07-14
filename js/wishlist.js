/* ===================== SEARCH ===================== */

function searchWishlist(){

    let input = document.getElementById("searchInput");

    let filter = input.value.toUpperCase();

    let cards = document.getElementsByClassName("wishlist-card");

    for(let i=0;i<cards.length;i++){

        let restaurant = cards[i]
            .getElementsByClassName("restaurant")[0]
            .innerText;

        let food = cards[i]
            .getElementsByClassName("food-name")[0]
            .innerText;

        if(
            restaurant.toUpperCase().indexOf(filter)>-1 ||
            food.toUpperCase().indexOf(filter)>-1
        ){

            cards[i].style.display="block";

        }else{

            cards[i].style.display="none";

        }

    }

}

/* ===================== REMOVE ===================== */

let removeButtons = document.querySelectorAll(".remove-btn");

removeButtons.forEach(function(button){

    button.addEventListener("click",function(){

        this.closest(".wishlist-card").remove();

    });

});

/* ===================== ADD TO CART ===================== */

let cartButtons = document.querySelectorAll(".cart-btn");

cartButtons.forEach(function(button){

    button.addEventListener("click",function(){

        alert("Item added to cart!");

    });

});