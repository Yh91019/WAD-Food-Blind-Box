const deliveryFee = 5;

function calculateTotal(){

let subtotal=0;

document.querySelectorAll("tbody tr").forEach(row=>{

let qty=parseInt(row.querySelector(".qty").value);

let price=parseFloat(row.querySelector(".price").textContent);

let itemSubtotal=qty*price;

row.querySelector(".subtotal").textContent=itemSubtotal.toFixed(2);

subtotal+=itemSubtotal;

});

document.getElementById("subtotal").textContent=subtotal.toFixed(2);

let fee=0;

if(document.querySelector("input[value='delivery']").checked){

fee=deliveryFee;

}

document.getElementById("deliveryFee").textContent=fee.toFixed(2);

document.getElementById("grandTotal").textContent=(subtotal+fee).toFixed(2);

}

document.querySelectorAll(".plus").forEach(button=>{

button.onclick=function(){

let qty=this.parentElement.querySelector(".qty");

qty.value=parseInt(qty.value)+1;

calculateTotal();

}

});

document.querySelectorAll(".minus").forEach(button=>{

button.onclick=function(){

let qty=this.parentElement.querySelector(".qty");

if(parseInt(qty.value)>1){

qty.value=parseInt(qty.value)-1;

}

calculateTotal();

}

});

document.querySelectorAll(".remove-btn").forEach(button=>{

button.onclick=function(){

if(confirm("Remove this item from cart?")){

this.closest("tr").remove();

calculateTotal();

}

}

});

document.querySelectorAll("input[name='delivery']").forEach(radio=>{

radio.onchange=calculateTotal;

});

calculateTotal();
