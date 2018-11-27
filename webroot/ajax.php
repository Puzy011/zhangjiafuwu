<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
    <script>
       $(function(){
           $.ajax({
               url:'http://120.24.172.108:8889/cgi-bin/key/makeSdkKey/F144BAEEFB10560B8A1B8D43FFEAF7D1',
               data:{"requestParam":{"deviceIds":[7],"keyEffecDay":"365"},
                     "header":{"signature":"813c8589-91a5-495e-9cab-b517b320f483","token":"1494298012345"}
                    },
               dataType:'POST',
               success(data){
                   console.log(data);
               }
           })
       })
    </script>
</body>
</html>