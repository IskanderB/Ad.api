require('./bootstrap');

// Set box in height center
let clientHeight = document.documentElement.clientHeight;
let boxHeight = $(".wrap")[0].offsetHeight;

let marginTop = 0.8*(clientHeight - boxHeight)/2 + "px";
$(".wrap")[0].style.marginTop = marginTop;

// Set current documentation link in <a>
let a = document.getElementsByClassName('documentation_link');

for (var i = 0; i < a.length; i++) {
    a[i].href ='/documentation';
}

// Set host name to label
let host = location.hostname;
let label = document.getElementById('host_name');
label.innerText = host;
