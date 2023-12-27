# anp-dns-manager
Manage Your Cloudflare Domains, Update Your Dynamic IP Automatically.
</br>
</br><center>
<img class="col col-md rounded" src="https://home-wifi.kanthzone.com/public_api.php?token=df33a4141af9b8e0d9aab3f8b871900d&type=view" style="width:800px;"/>
</center>
</br></br>
<h1>Docker Version</h1>

<h5>Version 1.0.0</h5>
Service Compatability:
<ul>
  <li>This Initial Version Supports 1 Domain Management For Now</li>
  <li>Backend API Supports Cloudflare API Version 4</li>
  <li>Managing Domain Locally Only. This version does not manage Cloudflare subdomains for now. CREATING, UPDATING & DELETING in cloudflare must be done manually. </li>
</ul>

The Tools:
<ul>
  <li>Allows to update your Public Dynamic IP into your subdomains.</li>
  <li>Integrated tool will constantly monitor IP changes</li>
  <li>Web User Interface to manage your subdomains</li>
  
</ul>

The Environment:
<ul>
  <li>Must run in the network that requires the Dynamic IP update. <i>Example: Your home network.</i></li>
  <li><b>Recommended:</b> To run in Raspberry Pi Or any Micro sized Vm</li>
</ul>

Tested Environment:
<ul>
  <li>Developed and tested on 
    <ul>
      <li>Ubuntu 22.04.2</li>
      <li>Docker version 24.0.7, build afdd53b</li>
    </ul>
  </li>
  
</ul>

<h1>Installation NOTE:</h1>

The Composer comes with complete tools of
<ul>
  <li>Ningx Webserver</li>
  <li>PHP</li>
  <li>Mysql Server</li>
</ul>
<br>
Download this repo and run

``` . install ``` 

Once the installation is done open your browser and navigate to your system IP with port 8888.<p>
Example: http://192.168.1.131:8888
</br></br>
<img class="col col-md rounded" src="https://home-wifi.kanthzone.com/public_api.php?token=db317d19dfdf18df88859ddcfc80c36f&type=view" style="width:500px;"/><p>
You should get the installer screen
</br></br>
Just follow the steps to complete the installation. <p><b>Remember: You cannot change the domain name after installation.<br></br>
Once the WUI has been installed you can immediately start using the ANP DNS Manager v1.</b><br>
<p>
<img class="col col-md rounded" src="https://home-wifi.kanthzone.com/public_api.php?token=4dfa4329627eaaaf3ec6ab11a171e665&type=view" style="width:800px;"/>
</br>
<b>Remember: When creating new domain, only insert the subdomain that you prefer and not the full domain.</b><p></br>
Example: The subdomain that i want is <b>test-beta.example.com</b><p>
On the DNS Manager i should only input <b>"test-beta"</b> on the sudbomain input box.
<p></p><br></br>
<hr>

<h4>System Directories:</h4><p>
  
```
-docker-compose.yml
-Dockerfile
-Dockerfile_nginx
-html/
-nginx/
-entrypoint/
   -services.sh
```

<hr>
<h1>Customizing Installation</h1>
<p>
  If you do not wish to use the MySQL or Nginx in the composer you may opt them</p>
  <strong>Remember: It is not recomended to change the PHP container as it contains microservices that requires to run the backend tools. </strong>
</br></br>
However, you may use your own MySQL server.<p></p>
By changing the DB parameters in the file

```./html/config/db.php```
<p></p>
At the Line number 19 - 22<p>
you may modify these lines according to your setup.<p>
  
```
      define("SERVER", "Your DB server IP");
      define('DB', 'Your Preferred Database Name');
      define("DB_ID", "Your DB username");
      define('DB_PWD', 'Your DB Passowrd');
```
</br>


  <hr>
  </br>
  <h1>Usage Note:</h1><br>
  1.) Create your subdomain in Cloudflare<br>
  2.) Create the same subdomain in ANP-DNS-Manager<br>
  3.) The system will automatically monitor for changes and update the domain with your network IP.<br>


<h1>Backend Explained:</h1><br>
The system purely uses our own cloud server and Cloudflare to retrieve your dynamic network public ip.<p>
<br>
<strong>Data Privacy: </strong></p>
</br>

On that note just to keep a track of our repo, and the system availability your dockerized system will make a call back to our server with your email and domain in the encrypted format of MD5, So dont worry we still wont know your email or domain. It's purely for your safe keeping, we only need this encrypted strings for our server usage statistics.

<br>
<hr>
<h1>Troubleshooting</h1>
<br>
By clicking on the button "Status" you will be able to see the recorded replies in the output like in below.<br>
<img class="col col-md rounded" src="https://home-wifi.kanthzone.com/public_api.php?token=313fd744cdd2540c5271685a167711eb&type=view" style="width:500px;"/>


<hr>

<h1>ARTICLE</h1>
Read more about it's features and notes here: https://writings.kanthzone.com/dynamic-ip-and-dns-12b21e4ab045 
