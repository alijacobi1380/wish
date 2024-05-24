<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password | WishTube</title>
    <style>
        #container {
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #child {
            width: 600px;
        }
        #child_txt {
            text-align: left;
            padding: 10px;
        }
        #child_txt h3 {
            color: #1877F2;
        }
        #child_bottom {
            border-bottom: 20px solid #1876f263;
        }
        #txt_blue {
            color: #1877F2;
        }
    </style>
</head>
<body>
        <div id="container">
            <div id="child">
                <img src="{{$message->embed('emailimg/ForgetPassImg.jpg');}}" alt="ForgetPassImg">
                <div id="child_txt">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Perspiciatis, dolorum modi. Quam eligendi illo ducimus sint molestias ipsum, est quos eveniet consectetur.</p>
                    <h3>Dear {{$user->name}} , Use Code Blow To Reset Your Password . </h3>
                    <div>
                        <h4>Confrimation Code Is : </h4> <h2 id="txt_blue">{{$key}}</h2>
                    </div>
                    <br><br><hr> 
                    <div id="child_bottom">
                        <h5>if you have any issues for reseting your Password or anything about your account conatct our suppurt team with email addres blow : <h5 class="txt_blue">
                            <a href="mailto:support@wishtube.com" target="_blank">support@wishtube.com</a>
                        </h5></h5>
                    </div>
                </div>
            </div>
        </div>
</body>
</html>