<!doctype html>
<html>
  <head>
    <title>Mvtch - Powerful image match finder.</title>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
    <script type="text/javascript">
      $(function() {
        var view = {
          $input: $('.input'),
          $inputBoxes: $('input', this.$el),
          $results: $('.results'),
          $submit: $('span.submit')
        };
        
        var model = {
          validateURLs: function(inputs, callback) {
            var error = false;
            inputs.each(function(i, el) {
              // a regex is sub-optimal, but I didn't want to include another library for URL parsing
              var urlregex = new RegExp("^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$");
              if(!urlregex.test($(el).val())) {
                error = true;
                return callback(false, $(el));
              } else {
                $(el).removeClass('error');
              }
            });
            return (!error) ? callback(true) : false;
          },
          
          updateResults: function(inputs, view) {
            var self = this;
            self.showLoading(view);
            $.ajax({
              url: 'req/matches.php',
              type: 'post',
              data: inputs.serialize()
            }).success(function(res) {
              self.removeLoading(function() {
                console.log('success');
                view.html(res);
              });
            });
          },
          
          showLoading: function(el) {
            var loadiv = $('<div>').addClass('loadiv').html('<img src="img/loading.gif"/> Loading').css({ opacity: 0 });
            loadiv.prependTo(el).animate({ opacity: 1 });
          },
          
          removeLoading: function(callback) {
            return $('.loadiv').animate({ opacity: 0 }, function() {
              return callback();
              $(this).remove();
            });
          },
          
          shakeView: function($el, shakes) {
            var border = $el.css('border');
            var shakes = shakes || 3;
            $el.addClass('error');
            for(var i =1; i < shakes; i++)
            {
              $el.animate({ 'margin-left' : '-=15px'}, 30)
                .animate({ 'margin-left' : '+=15px'}, 30)
                .animate({ 'margin-left' : '+=15px'}, 30)
                .animate({ 'margin-left' : '-=15px'}, 30);
            }
          }
        };
        
        view.$submit.click(function() {
          model.validateURLs(view.$inputBoxes, function(valid, el) {
            if(valid) {
              model.updateResults(view.$inputBoxes, view.$results);
            } else {
              model.shakeView(el);
            }
          });
        });
        
        view.$inputBoxes.keydown(function(e) {
          if(e.keyCode == 13) {
            view.$submit.click();
          }
        });
      });      
    </script>
    <style type="text/css">
      /**
       * This would typically be in a stylesheet, but I figured it would be 
       * easier to see if I just embeded it here
       */
      body, html { margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;}
      .container { margin: 20px auto; width: 600px; }
      h1 { margin: 40px auto 20px; text-align: center;}
      .input { padding: 20px 92px; border: 1px solid #ccc; overflow: auto; }
      .input span { font-size: 18px; }
      .input div input { font-size: 26px; padding: 5px; margin: 0 0 10px; border: 2px solid #ccc; width: 400px; display: block; }
      .input .submit { float: right; background: #333; color: #fff; padding: 5px; cursor: pointer; }
      .loadiv { margin: 10px auto; text-align: center;}
      .results { margin: 15px 0 0 0; width: 600px; text-align: center; }
      .results .imagematch { display: block; }
      .results>img { text-align: center; display: inline-block; margin: 0 10px 10px; }
    </style>
  </head>
  <body>
    <div class="container">
      <h1>Mvtch</h1>
      <div class="input">
        <span>Enter The URLs to Compare</span>
        <div>
          <input type="text" name="url1" placeholder="http://www.example.com" />
          <input type="text" name="url2" placeholder="http://www.compare.com" />
        </div>
        <span class="submit">Compare</span>
      </div>
      <div class="results">
      
      </div>
    </div>
  </body>
</html>