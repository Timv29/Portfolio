hideButton = document.getElementById("hideButton");

function updateNav(){
    nav = document.querySelector("nav");
    if(sessionStorage.getItem("menu") === "hidden" && window.innerWidth < 520){
        nav.style.display = "none";
    }
    else if(sessionStorage.getItem("menu") === "visible"){
        nav.style.display = "block";
    }   
}

updateNav();

hideButton.addEventListener("click", () => {
    nav = document.querySelector("nav");
    if(nav.checkVisibility()){
        sessionStorage.setItem("menu", "hidden");
        updateNav();
    }
    else{
        sessionStorage.setItem("menu", "visible");
        updateNav();
    }
})

window.addEventListener("resize", () => {
    if(window.innerWidth >= 520){
        nav.style.display = "block";
    }
})