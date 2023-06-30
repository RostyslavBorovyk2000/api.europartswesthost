<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <style>
        body {
            font-family: system-ui, sans-serif;
            margin: 0 !important;
            background: #56b0e4;
        }
    </style>
</head>
<body>
<img style="width:100%; height: 99.5vh; z-index: 0; position: relative" src="{{asset('img/img1.jpg')}}" alt="">
<div class="finish" style="color:white; position:absolute; z-index: 100; top: 27%; left:36%; font-size: 3vh; display:none;"> Збір завершено!! Вітаємо!
    <br>Збір було завершено замовленням №</div>
<div class="total" style="position: absolute;
    font-size: 15vh;
    display: flex;
    z-index: 100;
    top: 33.3%;
    left: 47%;
    letter-spacing: 6.8vh;
    color: #e4e4e4;
">
    <div class="total1" style="margin-right: 3.2vh"></div>
    <div class="total2"></div>
</div>
</body>
<script>
    let tot1 = document.querySelector('.total1');
    let tot2 = document.querySelector('.total2');
    let final = document.querySelector('.finish');
    function getTotal() {
        fetch('/api/total')
            .then((response) => response.json())
            .then((data) => {
                tot2.innerHTML = "";
                tot1.innerHTML = "";
                let num = data.total / 1000000;
                console.log(num);
                let arr = num.toString().split('.');
                let str = arr[1];
                for (let i = 0; i < str.length; i++) {
                    if (i >= 3 && i < 6) {
                        tot2.innerHTML += str[i];
                    } else {
                        if(i < 3){
                            tot1.innerHTML += str[i];
                        }
                    }
                }
                if(data.total >= 200000){
                    final.innerHTML = 'Збір завершено!! Вітаємо! <br> Збір було завершено замовленням №' + data.order;
                    final.style.display = 'block';
                }
            });

        return true;
    }

    console.log('start')
    setInterval(getTotal, 30000)
</script>
</html>
