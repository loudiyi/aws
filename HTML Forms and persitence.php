---
categories: Tutorials PHP HTML
...

# GET and POST

## The reflector

In this section we will improve an useful tool for debugging web
applications that we had started in [Tutorial 1](First steps in
PHP#do-you-speak-http): a request reflector.

1. Create a script `reflector.php` that prints out the contents of the
   `$_GET` array in an [HTML]() table. For example, upon a request to
   `reflector.php?car=peugeot&color=blue`, the output as seen in a
   browser should be similar to this:
   
<div style="margin:0 4em 0 4em; padding: 1em; border:solid thin black; overflow:scroll; max-height:8em;">
<style>
.pframe {border-collapse:collapse}
.pframe td {border: solid thin black;padding:4px}
</style>
<h4>GET parameters</h4>
<table class="pframe">
<tr><td>car</td><td>peugeot</td></tr>
<tr><td>color</td><td>blue</td></tr>
</table>
</div>

2. A little [XSS](). Visit the following [URL]():
   `reflector.php?<script>alert("XSS");</script>`. View the source
   [HTML]() code of the generated page (`Ctrl+U` on most
   browsers). Ikes!!! Use
   [`str_replace`](http://www.php.net/manual/function.str-replace.php)
   and [HTML entities]() to fix this.
   
3. Read the doc of
[`htmlspecialchars`](http://www.php.net/manual/function.htmlspecialchars.php).

4. Modify the script so that it also displays the contents of the
`$_POST` array. Test your script using the minibrowser you wrote in
[Tutorial 1](First steps in PHP#post).


## HTML forms

Create a page `forms.html` containing a web form with the input
controls of your choice. 

1. Include in the form at least one **reset**,
    one **button** and two **submit** controls like this:

 ~~~ {.html}
 <input type="button" name="b" value="Something" />
 <input type="reset" name="r" value="Erase" />
 <input type="submit" name="submit" value="Left" />
 <input type="submit" name="submit" value="Right" />
 ~~~

 Test the form by submitting data to `reflector.php` using both the
 GET and the POST methods.
 
2. Test the `<input type="image">` control.


# Client-side persistence

By *persistence* we mean the ability of the server to identify a
client across multiple [HTTP]() requests. In this section we are going
to make a tour of the various persistence techniques at the disposal
of the web developer; our prototype will be a simple page counting the
number of times it has been visited by a given client.

## GET persistence

Create a script `countGET.php`. The output should be a simple web page
looking like this:

<div style="margin:0 4em 0 4em; padding: 1em; border:solid thin black; overflow:scroll; max-height:8em;">
<p>Hello,</p>
<p>You have visited this page X times</p>
<p><a href="">visit this page again</a></p>
</div>

where X is the number of times the client has visited the page;
clicking on the link points the user to the same page, with the
counter incremented by one.

**Suggestions:**

- Use a GET parameter, say `vcount` to count the number of visits. If
  it is not set, the number of visits by the client will be assumed to
  be zero.
- Use the value of `vcount` to generate the link.
- To convert a string to an integer, you can use
  [`intval`](http://www.php.net/manual/function.intval.php).


Like you did before, be sure check your inputs to protect the script
from [XSS]() [injections](Code injection). Here's some more thoughts
on security:

1. Modify your script so that after 1000 visits it prints "Dear
customer, to thank you of your fidelity we offer you a 1000 euros!".

2. Without modifying the script, and without clicking a thousand
times, win the 1000 euros.


## POST persistence

Write a script `countPOST.php` achieving the same result as before
using a web form and an `<input type="hidden"/>` tag.

1. Since POST data are harder (aren't they?) to hack than GET data,
you can now offer 10000 euros to the thousandth visit.

2. Install the [Web Developer]() extension for Firefox and explore the
"Forms" menu (in particular the "Display Form Details" option). Win
the 10000 euros without clicking a thousand times.


## Cookie persistence

Go read the documentation of the
[`setcookie`](http://www.php.net/manual/function.setcookie.php)
function. To test if the client has sent a given cookie, you can use
the [`isset`](http://www.php.net/manual/function.isset.php) function
like this:

~~~ {.php}
<?php
if (isset($_COOKIE['moncookie'])) {
 ...
} else {
 ...
}
?>
~~~

Be very careful: the `setcookie` function must be used before ANY data
is printed on the output of the [PHP]() script. Thus, even a script
like this may not work (depending on your `php.ini` configuration):
 
~~~ {.php}
 <?php
setcookie('moncookie');
?>
~~~
 
Indeed, the white space before the `<?php` marker is a character of
output.

1. Write a script `countCookie.php` achieving the same result as
before. This time, you don't even need a link back to the same page:
refreshing the page is enough to send the cookie back.
 
2. Since cookies are *really* hard to hack (of course they are!),
offer a million euros to the thousandth visit.

3. By default cookies expire after the browser is closed. Read the
documentation of
[`setcookie`](http://www.php.net/manual/function.setcookie.php) and
modify your script so that the cookie lasts one hour.

4. Using the [Web Developer]() "Cookies" menu, in particular the "View
Cookie Information" function, win the million euros.



# Server-side persistence

In the previous section we have seen that client-side persistence
cannot help against clients maliciously modifying session
data. Server-side persistence works the opposite way: the session data
is kept on the server in a temporary file, while only an ephemeral
identification string, called the **session id** is exchanged with the
client using the techniques seen above.

**Warning:** while a client cannot directly modify its own session
  data, it is still possible for her to cheat by guessing someone
  else's session id! This is called [Session theft]().

[PHP]() offers a transparent server-side
[session library](http://www.php.net/manual/book.session.php): it
automatically decides which client-side method will be used for
exchanging the session id ([Cookies](Cookie) by default, or GET
persistence if the client refuses cookies), and takes care of handling
temporary files.

The most important function of the library is
[`session_start`](http://www.php.net/manual/function.session-start.php),
which initializes the session and fills the `$_SESSION` array with
session data. Like the
[`setcookie`](http://www.php.net/manual/function.setcookie.php)
function, it must be called before any output is written.

**Important:**
  [`session_start`](http://www.php.net/manual/function.session-start.php)
  must be called **exactly once** at the beginning of any script that
  needs to handle session data. Starting a session and then including
  a script that starts again a session is a common mistake.

1. Write a script `countSession.php` that counts the number of visits
using [PHP]() sessions.
2. Is there any need to sanitize data? Do you think you can win the
million euros?
3. Using [Web Developer](), examine how [PHP]() handles session
cookies. How do you reset the counter to zero?
4. Talk to your web application using your command-line mini browser
from [tutorial 1](First steps in PHP#post).


# User accounts

## Debugging PHP

From now on, our [PHP]() programs are going to become complex enough
to need some though debugging. Since it is a security issue to show
execution errors to an attacker, most [PHP]() installations disable
error reporting in web servers.

Depending on your [PHP]() configuration, the runtime errors can be
read in the web server logs. However, for a web developer it
is much more comfortable to read errors in the browser. Two settings in the
[`php.ini`](http://www.php.net/manual/errorfunc.configuration.php)
configuration file control error reporting:

- The
  [`error_reporting`](http://www.php.net/manual/errorfunc.configuration.php)
  directive selects which errors are printed;
- The
  [`display_errors`](http://www.php.net/manual/errorfunc.configuration.php)
  decides where error messages are printed (in the browser, in the
  server logs, or not at all).
  
The two directives can be controlled, respectively, via the
[`error_reporting`](http://www.php.net/manual/function.error-reporting.php)
and the [`ini_set`](http://www.php.net/manual/function.ini-set.php)
functions. The following two lines will activate error reporting in
the browser:

~~~ {.php}
<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
?>
~~~

While the following line deactivates any error reporting (both in the
browser and on the command line.

~~~ {.php}
<?php
error_reporting(0);
?>
~~~

Test these functions using a buggy script like this:

~~~ {.php}
<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
dfadjhfadjf();  // unknown function
?>
~~~

Note, however, that parse errors (such as forgetting semi-colons `;`)
occur before the script output and cannot be read in the browser. In
this case the only way to debug your script is to run it from the
command line.

The Firefox extension [FirePHP](Firebug#firephp) helps inspecting
[PHP]() code by sending [AJAX]() messages to the browser. However
there are [PHP]() errors that cannot be caught by it.


## Accounts with Cookies

In this section we are going to reuse the pseudoframe structure we
studied in [tutorial 1](HTML, CSS and dynamic
layout#page-generation-using-php). We want to create a simple website
where users can register; the user data will be stored in one or more
cookies on the client side.

Create scripts `index.php`, `menu.php`, `welcome.php`,
`subscription.php` and `register.php` (never mind the footer).

1. `index.php` includes `menu.php` as a side menu and `welcome.php` or
`subscription.php` as main content, depending on a GET parameter.
2. `welcome.php` contains a greetings message. If the user has the
cookies properly set, it prints the user name and its personal
information; otherwise it prints a generic message and a link to the
subscription page.
3. `menu.php` contains a link to the Welcome page and a link to the
Subscription page. If the cookies aren't set, the second link reads
"Register"; if they are set, it reads "Edit your data".
4. `subscription.php` contains a [HTML]() form for entering the user
data (first and last name, birthday, email, ...). If the cookies are
set, the fields are pre-filled with the user data. Upon validation,
the form data is sent to the script `register.php`.
5. `register.php` parses the POST data it receives and saves them in
one or more cookies. Then, it redirects the user to the welcome page
using a `Location:` header (carefully read the
[`header`](http://www.php.net/manual/function.header.php)
documentation).
6. Talk to your web application using your command-line mini browser
from [tutorial 1](First steps in PHP#post).

Protect your scripts from [Code injections](). What data should you
sanitize? The POST data or the Cookie ones?

Here's a simplified solution to the above exercise, all in one file.

<div  style="max-height:6ex;overflow:hidden"
onclick="if(this.style.maxHeight!='none')this.style.maxHeight='none';else this.style.maxHeight='6ex'">

~~~ {.php}
<?php                  // Click here to show the solution
// Check if the user is registering or modifying its data
if (isset($_POST['username'])) {
  setcookie('user', $_POST['username']);
  // Redirect to the welcome page
  // (this is useful to forget post data)
  $host = $_SERVER['HTTP_HOST'];
  $script = $_SERVER['PHP_SELF'];
  header("Location: http://$host$script");
  exit(0);
}

// Check if the user is registered
if (isset($_COOKIE['user'])) {
  $name = htmlspecialchars($_COOKIE['user']);
  $greeting = "Hi! $name";
  $link = 'Edit your data';
} else {
  $name = "";
  $greeting = 'Welcome!';
  $link = 'Register';
}
?>
<!DOCTYPE html>
<html>
<head><title>User page</title></head>
<body>
<?php
if (isset($_GET['page']) && $_GET['page'] == 'subscribe') {
  echo "<h1>Please enter your data</h1>";
  echo "<form method='POST' action='?' />";
  echo "<p>Name: <input type='text' name='username' value='$name'/>";
  echo "<input type='submit' value='$link'/></p>";
  echo "</form>";
} else {
  echo "<h1>$greeting</h1>";
  echo "<p><a href='?page=subscribe'>$link</a></p>";
}
?>
</body>
</html>
~~~

</div>


## Accounts with sessions

1. Modify the previous script so that it uses [PHP]() sessions instead of
[cookies](Cookie).
2. Add a password field to the subscription form and allow the user to
modify its data only if she correctly re-types its password.
3. Add a form to change password (the old password must be re-typed
first).
