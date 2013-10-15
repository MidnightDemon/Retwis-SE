Retwis-SE
================================

[Redis](http://try.redis.io/) is a funny little NoSQL database, and very easy to learn!  I had originally viewed [this article on how Redis can be used with PHP to make a very simple twitter clone](http://redis.io/topics/twitter-clone).

But, the links were broken and (after some hunting) I found a copy of Retwis 0.3.  I found the library [written by Ludovico Magnocavallo](http://qix.it/)  - it's out-of-date and thus buggy so I've replaced it with Predis.  **I'm doing a fix on it and will include it in the project, just for reference.

**UPDATE: The library was using old request protocols - currently working on it.

Setup Instructions
-------------------------

We will need the following:

* PHP (>5.4.0) - contains built-in PHP Dev Server
* [Redis](http://redis.io/download) (> 2.6.0)
* [Composer PHP Dependency Manager](https://github.com/composer/composer)
* Optional - [Apache PHP Server](http://www.apache.org/)

Additional Notes
-------------------------------

The original base code uses shorthand tags - edit your php.ini and enable short_open_tags for now, I'll replace them when I get around to it.

To start the PHP built-in server, navigate into the folder you put this code into and use : php -S localhost:3000

I'm using Chromium on ArchLinux - which does not support 'localhost' ref, so I just use : php -S 127.0.0.1:3000 - use any port you like!

Start the Redis server using : redis-server  
Start the Redis client using : redis-cli

Copyright Stuff for the Base Code
------------------------

Copyright (c) 2006-2009, Salvatore Sanfilippo
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    * Neither the name of Redis nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
