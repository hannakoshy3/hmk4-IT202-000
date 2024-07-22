function flash(message = "", color = "info") {
    let flash = document.getElementById("flash");
    //create a div (or whatever wrapper we want)
    let outerDiv = document.createElement("div");
    outerDiv.className = "row justify-content-center";
    let innerDiv = document.createElement("div");

    //apply the CSS (these are bootstrap classes which we'll learn later)
    innerDiv.className = `alert alert-${color}`;
    //set the content
    innerDiv.innerText = message;

    outerDiv.appendChild(innerDiv);
    //add the element to the DOM (if we don't it merely exists in memory)
    flash.appendChild(outerDiv);
}
function validate(form) {
    //TODO 1: implement JavaScript validation
    //ensure it returns false for an error and true for success
    let email = form.email.value;
    let password = form.password.value;

    if (password.length < 8) {
        flash("[client Password must be at least 8 characters long.");
        return false;
    }

    

    //TODO update clientside validation to check if it should
    //valid email or username
    return true;
}
function validate(form) {
    //TODO 1: implement JavaScript validation
    //ensure it returns false for an error and true for success
    let email = form.email.value;
    let username = form.username.value;
    let password = form.password.value;
    let confirm = form.confirm.value;

    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailPattern.test(email)) {
        flash(" [client] Please enter a valid email address");
        return false;
    }

    const usernamePattern = /^[a-zA-Z0-9_-]{3,15}$/;
    if (!usernamePattern.test(username)) {
        flash("[client] Username must only contain 3-15 characters a-z, 0-9, _, or -");
        return false;
    }

    if (password.length < 8) {
        flash("[client] Password must be at least 8 characters long");
        return false;
    }

    if (password !== confirm) {
        flash("[client] Passwords must match");
        return false;
    }

    return true;
}
function validate(form) {
    let pw = form.newPassword.value;
    let con = form.confirmPassword.value;
    let isValid = true;
    //TODO add other client side validation....
    if (pw.length < 8) {
        flash("[client] password must be at least 8 characters long");
        return false;
    }

    //example of using flash via javascript
    //find the flash container, create a new element, appendChild
    if (pw !== con) {
        flash("Password and Confrim password must match", "warning");
        isValid = false;
    }
    return isValid;
}