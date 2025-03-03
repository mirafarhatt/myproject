document.addEventListener("DOMContentLoaded", function () {
    const headerHTML = `
       <header class="header">
           <div class="container">
               <div class="row">
                   <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col logo_section">
                       <div class="full">
                           <div class="center-desk">
                               <div class="logo" style="margin-top:-40px;margin-bottom: -30px;"> 
                                   <a href="Home.html"><img src="images/logo1.png" alt="#" width="150px"></a> 
                               </div>
                           </div>
                       </div>
                   </div>
                   <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                       <div class="menu-area">
                           <div class="limit-box">
                               <nav class="main-menu">
                                   <ul class="menu-area-main">
                                       <li><a href="Home.html">Home</a></li>
                                       <li><a href="library.php">Our Books</a></li>
                                       <li><a href="services.html">Our Services</a></li>
                                       <li><a href="about.html">About us</a></li>
                                       <li><a href="contact.html">Contact us</a></li>
                                       <li class="mean-last"><a href="cart.html"><img src="images/cart.png" alt="#"></a></li>
                                       <li id="auth-icon" class="mean-last"></li>
                                   </ul>
                               </nav>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </header>
   `;
    document.body.insertAdjacentHTML("afterbegin", headerHTML);

    // Fetch session data to determine login state
    fetch('header_data.php')
    .then(response => response.json())
    .then(data => {
        const authIcon = document.getElementById('auth-icon');
        if (data.logged_in) {
            authIcon.innerHTML = `
                <a href="logout.php" title="Logout">
                    <img src="images/logout.png" alt="Logout">
                </a>
            `;
        } else {
            authIcon.innerHTML = `
                <a href="login.html" title="Login">
                    <img src="images/top-icon.png" alt="Login">
                </a>
            `;
        }
    })
    .catch(error => {
        console.error('Error fetching session data:', error);
        const authIcon = document.getElementById('auth-icon');
        authIcon.innerHTML = `
            <a href="login.html" title="Login">
                <img src="images/top-icon.png" alt="Login">
            </a>
           `;
        });
});
