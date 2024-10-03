let button = document.getElementById('menuIcon');
let currentStateMenu = false;

let items = document.getElementsByClassName('items');
let itemHeaders = document.getElementsByClassName('itemHeader');

let opened = [false, false, false, false];

for(let i = 0; i < itemHeaders.length; i ++){
    itemHeaders[i].addEventListener('click', displayMenuItems);
}

button.addEventListener('click', displayMenu);

function displayMenu(){

    let menu = document.getElementById('navigation');
    let dim = document.getElementById('dim');

    if(currentStateMenu === false){
        menu.style.width = "20vw";
        dim.style.transitionDelay = "0s";
        dim.style.width = "100vw";
        currentStateMenu = true;
    }
    else{
        currentStateMenu = false;
        dim.style.transitionDelay = "0.5s";
        dim.style.width = "0px";
        menu.style.width = "0vw";

        for(let i = 0; i < itemHeaders.length; i++){
            items[i].style.height = "0px";
            items[i].style.borderBottomWidth = "0px";

            opened[i] = false;
        }
    }
}

function displayMenuItems(){
    
    let index;

    for(let i = 0; i < itemHeaders.length; i++){

        if(itemHeaders[i] == this){
            index = i;
        }

    }

    for(let i = 0; i < items.length; i++){

        if(items[i] != items[index]){
            items[i].style.height = "0px";

            opened[i] = false;
        }

        else{

            if(opened[i] == true){
                items[i].style.height = "0px";

                opened[i] = false;
            }

            else{
                items[i].style.height = (items[i].children.length)*38 + "px";
                opened[i] = true;
            }
        }
    }
}