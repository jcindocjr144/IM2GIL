    
    function grades(){
        document.getElementById("grades").style.display = "block";
        document.getElementById("info").style.display = "none";
    }
    function info(){
        document.getElementById("info").style.display = "block";
        document.getElementById("grades").style.display = "none";
    }
    function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
    document.getElementById("main").style.marginLeft = "250px";
    }
    function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.getElementById("main").style.marginLeft = "0";
    }