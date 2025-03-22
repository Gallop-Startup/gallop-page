document.addEventListener("DOMContentLoaded", function() {
    console.log("contact_form.js loaded");
    const form = document.getElementById("contact-form");

    form.addEventListener("submit", function(e) {
        e.preventDefault();

        console.log("Sending contact form data...");
        fetch("mail/contact_me.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded" 
            },
            body: new URLSearchParams(new FormData(form))
        })
        .then(response => {
            return response.text().then(text => {
                if (response.ok && text.trim() === "success") {
                    alert("Your message has been sent successfully!");
                    form.reset();
                } else {
                    console.error("PHP Error Response:", text);
                    alert("There was an error sending your message. Please try again.");
                }
            });
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            alert("There was an error sending your message. Please try again.");
        });
    });
});