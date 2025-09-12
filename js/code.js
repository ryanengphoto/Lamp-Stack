const urlBase = 'http://COP4331-5.com/LAMPAPI';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";

function doLogin()
{
  userId = 0; firstName = ""; lastName = "";

  const login = document.getElementById("loginName").value.trim();
  const password = document.getElementById("loginPassword").value;
  const resultEl = document.getElementById("loginResult");
  const btn = document.getElementById("loginButton");
  const spinner = document.getElementById("loginSpinner");
  const btnText = document.getElementById("loginBtnText");
  const remember = document.getElementById("rememberMe") ? document.getElementById("rememberMe").checked : false;

  // quick client-side validation
  if (!login || !password) {
    resultEl.textContent = "Please enter a username and password.";
    bump("#loginDiv");
    return;
  }

  // show loading state
  if (btn) btn.disabled = true;
  if (spinner) spinner.style.display = "inline-block";
  if (btnText) btnText.textContent = "Signing in…";
  resultEl.textContent = "";

  // Optional: hash if your backend expects it
  // const hash = md5(password);
  const tmp = { login: login, password: password };
  const jsonPayload = JSON.stringify(tmp);

  // IMPORTANT: make sure urlBase matches your domain/path
  const url = urlBase + '/Login.' + extension;

  const xhr = new XMLHttpRequest();
  xhr.open("POST", url, true);
  xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

  xhr.onreadystatechange = function()
  {
    if (this.readyState !== 4) return;

    // revert loading state
    if (btn) btn.disabled = false;
    if (spinner) spinner.style.display = "none";
    if (btnText) btnText.textContent = "Do It";

    if (this.status === 200)
    {
      try {
        const jsonObject = JSON.parse(xhr.responseText);
        userId = jsonObject.id;

        if (userId < 1) {
          resultEl.textContent = "Hmm… that combo doesn’t look right. Try again?";
          bump("#loginDiv");
          return;
        }

        firstName = jsonObject.firstName || "";
        lastName = jsonObject.lastName || "";

        // cookie lifespan based on "remember me"
        saveCookie(remember ? 24*60 : 20); // minutes

        // tiny confetti then redirect
        confettiBurst();
        setTimeout(() => { window.location.href = "color.html"; }, 600);
      } catch(e) {
        resultEl.textContent = "Unexpected response from server.";
        bump("#loginDiv");
      }
    }
    else
    {
      resultEl.textContent = "Login failed (network/server). Please try again.";
      bump("#loginDiv");
    }
  };

  xhr.onerror = function() {
    if (btn) btn.disabled = false;
    if (spinner) spinner.style.display = "none";
    if (btnText) btnText.textContent = "Do It";
    resultEl.textContent = "Network error. Check your connection and try again.";
    bump("#loginDiv");
  };

  try { xhr.send(jsonPayload); }
  catch(err) {
    if (btn) btn.disabled = false;
    if (spinner) spinner.style.display = "none";
    if (btnText) btnText.textContent = "Do It";
    resultEl.textContent = err.message;
    bump("#loginDiv");
  }
}


function saveCookie(minutes)
{
  const date = new Date();
  date.setTime(date.getTime() + (minutes * 60 * 1000));
  document.cookie = "firstName=" + firstName +
                    ",lastName=" + lastName +
                    ",userId=" + userId +
                    ";expires=" + date.toGMTString() + ";path=/";
}


function readCookie()
{
	userId = -1;
	let data = document.cookie;
	let splits = data.split(",");
	for(var i = 0; i < splits.length; i++) 
	{
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		if( tokens[0] == "firstName" )
		{
			firstName = tokens[1];
		}
		else if( tokens[0] == "lastName" )
		{
			lastName = tokens[1];
		}
		else if( tokens[0] == "userId" )
		{
			userId = parseInt( tokens[1].trim() );
		}
	}
	
	if( userId < 0 )
	{
		window.location.href = "index.html";
	}
	else
	{
//		document.getElementById("userName").innerHTML = "Logged in as " + firstName + " " + lastName;
	}
}

function doLogout()
{
	userId = 0;
	firstName = "";
	lastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}

function addColor()
{
	let newColor = document.getElementById("colorText").value;
	document.getElementById("colorAddResult").innerHTML = "";

	let tmp = {color:newColor,userId,userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/AddColor.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("colorAddResult").innerHTML = "Color has been added";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("colorAddResult").innerHTML = err.message;
	}
	
}

function searchColor()
{
	let srch = document.getElementById("searchText").value;
	document.getElementById("colorSearchResult").innerHTML = "";
	
	let colorList = "";

	let tmp = {search:srch,userId:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/SearchColors.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("colorSearchResult").innerHTML = "Color(s) has been retrieved";
				let jsonObject = JSON.parse( xhr.responseText );
				
				for( let i=0; i<jsonObject.results.length; i++ )
				{
					colorList += jsonObject.results[i];
					if( i < jsonObject.results.length - 1 )
					{
						colorList += "<br />\r\n";
					}
				}
				
				document.getElementsByTagName("p")[0].innerHTML = colorList;
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("colorSearchResult").innerHTML = err.message;
	}
	
}

function bump(selector) {
  const el = document.querySelector(selector);
  if (!el) return;
  el.classList.remove('shake');
  // reflow so the animation can replay
  void el.offsetWidth;
  el.classList.add('shake');
}

function confettiBurst() {
  const host = document.getElementById('confetti');
  if (!host) return;
  for (let i = 0; i < 24; i++) {
    const p = document.createElement('div');
    p.className = 'confetti-piece';
    p.style.left = (50 + (Math.random()*20 - 10)) + 'vw';
    p.style.top = '30vh';
    p.style.background = ['#ff6b6b','#ffd93d','#6bcB77','#4d96ff','#e056fd'][Math.floor(Math.random()*5)];
    p.style.animationDelay = (Math.random()*0.2) + 's';
    p.style.transform = `translateY(-20px) rotate(${Math.random()*180}deg)`;
    host.appendChild(p);
    setTimeout(() => host.removeChild(p), 1200);
  }
}

