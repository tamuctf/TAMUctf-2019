(function($) {
  "use strict"; // Start of use strict

  // Make the code input and output fill the screen nice.
  $(window).resize(function() {
    $(".workspace").map(function() {
      const spare = $(this).height() - $(this).children().outerHeight();
      $("#code-area").height((_, h) => h + spare*0.6);
      $("#output").height((_, h) => h + spare*0.4);
    })
  }).trigger("resize");
  
  $('#logout-btn').click(function() {
    alert("Not important to the challenge; didn't implement it.")
  });

  // Connect to socket.io backend for submitting code and getting updates.
  const socket = io.connect('http://' + document.domain + ':' + location.port);
  socket.on('connect', function() {
    console.log("connected to backend")
  });
  socket.on('disconnect', function() {
    console.log("disconnected from backend")
  });
  socket.on('update', function(data) {
    $("#output").text((_, x) => [x,data.stdout,data.stderr].join(''));
  });
  $('#submit').click(function() {
    $('#output').text('')
    $('#code-area').map(function() {
      if (this.value && this.value.length > 0) {
        socket.emit('submit', {code: this.value});
      }
    });
  });
})(jQuery);
