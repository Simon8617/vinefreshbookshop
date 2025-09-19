document.addEventListener("DOMContentLoaded", function() {
  const form = document.getElementById("registerForm");
  if(form){
    form.addEventListener("submit", function(e){
      e.preventDefault();
      let formData = new FormData(form);
      fetch("backend/register_process.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.text())
      .then(data => {
        document.getElementById("result").innerHTML = data;
        form.reset();
      });
    });
  }
});
