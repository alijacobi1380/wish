<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | WishTube</title>
    <style>
        #container {
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #child {
            width: 600px !important;
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

        #child_button {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #button {
            border-radius: 10px;
            text-align: center;
            width: 50%;
            background-color: #1877F2;
            padding: 14px;
            margin: 20px 0;
            color: white;
            text-decoration: none;
        }

        #child_button_support {
            margin-top: 60px;
        }
    </style>
</head>

<body>
    <div id="container">
        <div id="child">
            <img src="{{$message->embed('emailimg/VerifyEmail.jpg')}}"
                alt="ForgetPassImg">
            <div id="child_txt">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Perspiciatis, dolorum modi. Quam eligendi
                    illo ducimus sint molestias ipsum, est quos eveniet consectetur.</p>
                <h3>Welcome to Wishtube , Use Button Below To Confrim Your Email</h3>
                <div id="child_button">
                    <a id="button" href="{{ route('accepctemail', ['code' => $key]) }}">Verify Email</a>
                </div>
                <div id="child_button_support">
                    if button isnt working , use this link by clicking on it : <a
                        href="{{ route('accepctemail', ['code' => $key]) }}" target="_blank">Lorem
                        {{ route('accepctemail', ['code' => $key]) }}</a>
                </div>
                <br><br>
                <hr>
                <div id="child_bottom">
                    <h5>if you have any issues for reseting your Password or anything about your account conatct our
                        suppurt team with email addres blow : <h5 id="txt_blue">
                            <a href="#" target="_blank">support@wishtube.com</a>
                        </h5>
                    </h5>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
