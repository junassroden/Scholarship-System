document.addEventListener("DOMContentLoaded", function () {

    const roleInput = document.getElementById("role");
    const studentFields = document.getElementById("studentFields");
    const organizerFields = document.getElementById("organizerFields");

    if (!roleInput) return;

    const role = roleInput.value;

    if (role === "organizers") {
        organizerFields.style.display = "block";
        studentFields.style.display = "none";
    } else {
        organizerFields.style.display = "none";
        studentFields.style.display = "block";
    }

});