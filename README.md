# anp-dns-manager
Manage Your Cloudflare Domains, Update Your Dynamic IP Automatically.

<h1>Docker Version</h1>

<h5>Version 1.0.0</h5>
Service Compatability:
<ul>
  <li>This Initial Version Supports 1 Domain Management For Now</li>
  <li>Backend API Supports Cloudflare API Version 4</li>
  <li>Managing Domain Locally Only. This version not ready to manage Cloudflare subdomains for now. CREATING, UPDATING & DELETING in cloudflare must be done manually. </li>
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
      <li>UBUNTU 23.0.4 </li>
      <li>Docker</li>
    </ul>
  </li>
  
</ul>

<h1>NOTE:</h1>

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

to initiate the installation.

once the WUI has been installed you can immediately start using the ANP DNS Manager v1.

System Directories:<p>
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
  If you do not wish to use the PHP , MySQL or Nginx in the composer you may opt them</p>
  <strong>Remember: It's not recomended to change the PHP container as it contains microservices that requires to run this web tool. </strong>

  <hr>
  </br>
  <h1>Usage Note:</h1><br>
  1.) Create your subdomain in Cloudflare<br>
  2.) Create the same subdomain in ANP-DNS-Manager<br>
  3.) The system will automatically monitor for changes and update the domain with your network IP.<br>
