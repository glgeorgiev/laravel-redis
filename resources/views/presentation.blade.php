<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>Redis - Laravel's best friend | Presentation by Georgi Georgiev</title>

    <link rel="stylesheet" href="reveal.js-3.5.0/css/reveal.css">
    <link rel="stylesheet" href="reveal.js-3.5.0/css/theme/black.css">

    <!-- Theme used for syntax highlighting of code -->
    <link rel="stylesheet" href="reveal.js-3.5.0/lib/css/zenburn.css">

    <!-- Printing and PDF exports -->
    <script>
        var link = document.createElement( 'link' );
        link.rel = 'stylesheet';
        link.type = 'text/css';
        link.href = window.location.search.match( /print-pdf/gi ) ? 'reveal.js-3.5.0/css/print/pdf.css' : 'reveal.js-3.5.0/css/print/paper.css';
        document.getElementsByTagName( 'head' )[0].appendChild( link );
    </script>
    <style>
        img {
            max-height: 500px !important;
        }
    </style>
</head>
<body>
<div class="reveal">
    <div class="slides">
        <section>
            <h1>Redis - Laravel's best friend</h1>
        </section>
        <section>
            <h2>About me</h2>
            <p>Georgi Georgiev</p>
            <ul>
                <li>Backend web developer at <a href="https://maniaci.net" target="_blank">Maniaci.Net</a></li>
                <li>Using Laravel for two and a half years</li>
                <li>Likes to run SQL queries on production</li>
            </ul>
        </section>
        <section>
            <h2>Agenda</h2>
            <ol>
                <li>What is Redis?</li>
                <li>How to install Redis?</li>
                <li>When and how to use Redis in Laravel?</li>
                <li>Advanced usage</li>
                <li>Overview</li>
            </ol>
        </section>
        <section>
            <section>
                <h2>1. What is Redis?</h2>
                <img src="img/1.jpg" alt="1">
            </section>
            <section>
                <img src="img/redis.png" alt="Redis logo">
                <p>Redis is an open source (BSD licensed), in-memory data structure store, used as a database, cache and message broker.</p>
            </section>
            <section>
                <p>Redis supports data structures like:</p>
                <ul>
                    <li>strings</li>
                    <li>hashes</li>
                    <li>lists</li>
                    <li>sets</li>
                </ul>
            </section>
            <section>
                <p>Redis also supports multiple databases per instance.</p>
            </section>
        </section>
        <section>
            <section>
                <h2>2. How to install Redis?</h2>
                <img src="img/2.jpg" alt="2">
            </section>
            <section>
                <h3>2.1. For Ubuntu 14.04/Mint 17</h3>
                <pre><code class="nohighlight"># add-apt-repository ppa:chris-lea/redis-server</code></pre>
                <pre><code class="nohighlight"># apt-get update</code></pre>
                <pre><code class="nohighlight"># apt-get install redis-server</code></pre>
                <pre><code class="nohighlight"># service redis-server start</code></pre>
            </section>
            <section>
                <h3>2.2. For CentOS 7</h3>
                <pre><code class="nohighlight"># yum install epel-release</code></pre>
                <pre><code class="nohighlight"># yum install redis</code></pre>
                <pre><code class="nohighlight"># service redis start</code></pre>
            </section>
            <section>
                <h3>2.3. Laravel Homestead</h3>
                <p>You already have Redis :)</p>
            </section>
            <section>
                <h3>2.4. Redis CLI</h3>
                <pre><code class="nohighlight">$ redis-cli</code></pre>
                <pre><code class="nohighlight">127.0.0.1:6379> KEYS *
(empty list or set)
127.0.0.1:6379> SET foo bar
OK
127.0.0.1:6379> KEYS *
1) "foo"
127.0.0.1:6379> GET foo
"bar"
127.0.0.1:6379> DEL foo
(integer) 1
127.0.0.1:6379> GET foo
(nil)
127.0.0.1:6379> KEYS *
(empty list or set)</code></pre>
            </section>
        </section>
        <section>
            <section>
                <h2>3. When and how to use Redis in Laravel?</h2>
                <img src="img/3.png" alt="3" style="background-color: #fff">
            </section>
            <section>
                <p>You will need the Predis driver</p>
                <pre><code class="nohighlight">$ composer require predis/predis</code></pre>
            </section>
            <section>
                <h3>3.1. Sessions.</h3>
                <p>Update the .env<p>
                <pre><code>SESSION_DRIVER=redis</code></pre>
                <p>Even better, you can edit the config/session.php file:</p>
                <pre><code>'connection' => 'sessions',</code></pre>
            </section>
            <section>
                <p>And create the connection inside the config/database.php file:</p>
                <pre><code>'redis' => [
    'client' => 'predis',
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => 0,
    ],
    'sessions' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => 1,
    ],
],</code></pre>
            </section>
            <section>
                <h3>3.2. Cache.</h3>
                <p>Update the .env<p>
                <pre><code>CACHE_DRIVER=redis</code></pre>
            </section>
            <section>
                <p>Example 1</p>
                <pre><code>if (Cache::has('articles')) {
    $articles = Cache::get('articles');
} else {
    $articles = Article::latest(10)->get();

    Cache::put('articles', $articles, 5);
}</code></pre>
            </section>
            <section>
                <p>Example 2</p>
                <pre><code>//getting the logo
if (Cache::has('logo')) {
    $logo = Cache::get('logo');
} else {
    $logo = Logo::where('active', true)->first();

    Cache::forever('logo', $logo);
}

//updating the logo
$logo->active = true;
$logo->save();

Cache::forget('logo');</code></pre>
            </section>
            <section>
                <p>Example 3</p>
                <pre><code>if (Cache::has('view:welcome')) {
    return Cache::get('view:welcome');
}

$view = View::make('welcome')->render();

Cache::put('view:welcome', $view, 1);

return $view;</code></pre>
            </section>
            <section>
                <p>Example 4</p>
                <pre><code>Redis::rPush('articles', serialize($request->all()));

for ($i = 0; $i < Redis::lLen('articles'); $i++) {
    Article::create(unserialize(Redis::lPop('articles')));
}</code></pre>
            </section>
            <section>
                <p>Example 5</p>
                <pre><code>Redis::hMset('app', [
    'url' => 'http://redis.maniaci.net',
    'name' => 'Redis - Laravel\'s best friend',
]);

$appUrl = Redis::hGet('app', 'url');
$appName = Redis::hGet('app', 'name');</code></pre>
            </section>
        </section>
        <section>
            <section>
                <h2>4. Advanced usage</h2>
                <img src="img/4.png" alt="4">
            </section>
            <section>
                <h3>4.1. Redis config file</h3>
                <pre><code class="nohighlight">$ vim /etc/redis/redis.conf</code></pre>
            </section>
            <section>
                <p>Network</p>
                <pre><code class="nohighlight">bind 127.0.0.1</code></pre>
                <pre><code class="nohighlight">bind 192.168.1.100 10.0.0.1</code></pre>
            </section>
            <section>
                <p>Security</p>
                <pre><code class="nohighlight">requirepass extremelysecretpassword</code></pre>
                <pre><code class="nohighlight">rename-command CONFIG neizpalnyavaytazikomanda</code></pre>
                <pre><code class="nohighlight">rename-command CONFIG ""</code></pre>
            </section>
            <section>
                <p>Protected mode</p>
                <pre><code class="nohighlight">protected-mode yes</code></pre>
            </section>
            <section>
                <p>Number of databases</p>
                <pre><code class="nohighlight">databases 16</code></pre>
            </section>
            <section>
                <p>Snapshotting</p>
                <pre><code class="nohighlight">save 900 1
save 300 10
save 60 10000</code></pre>
                <pre><code class="nohighlight">dbfilename dump.rdb</code></pre>
                <pre><code class="nohighlight">dir /var/lib/redis</code></pre>
            </section>
            <section>
                <p>Replication</p>
                <img src="img/tree.png" alt="3" style="background-color: #fff">
            </section>
            <section>
                <pre><code class="nohighlight">slaveof 192.168.1.101 6379</code></pre>
                <pre><code class="nohighlight">masterauth anotherstrongpassword</code></pre>
                <pre><code class="nohighlight"># Should the slave reply to client requests
# if not in sync with master
slave-serve-stale-data yes</code></pre>
                <pre><code class="nohighlight">slave-read-only yes</code></pre>
            </section>
            <section>
                <p>Cluster</p>
                <pre><code class="nohighlight">cluster-enabled yes</code></pre>
                <pre><code class="nohighlight">cluster-config-file nodes-6379.conf</code></pre>
                <pre><code class="nohighlight">cluster-node-timeout 15000</code></pre>
            </section>
            <section>
                <h3>4.2. Sentinel</h3>
                <p>Redis Sentinel provides high availability for Redis.</p>
                <p>List of Sentinel capabilities:</p>
                <ul>
                    <li>Monitoring</li>
                    <li>Notification</li>
                    <li>Automatic failover</li>
                    <li>Configuration provider</li>
                </ul>
            </section>
            <section>
                <pre><code class="nohighlight"># redis-sentinel /path/to/sentinel.conf</code></pre>
                <p>Default Sentinel port is 26379</p>
            </section>
        </section>
        <section>
            <section>
                <h2>5. Overview</h2>
                <img src="img/5.png" alt="5" style="background-color: #fff;">
            </section>
            <section>
                <ul>
                    <li>Easy to install and setup</li>
                    <li>Easy to use</li>
                    <li>Excellent performance</li>
                    <li>Replication and clustering</li>
                </ul>
            </section>
        </section>
        <section>
            <h2>Time for questions</h2>
            <img src="img/ihavenoidea.jpg" alt="I Have No Idea What I'm Doing">
        </section>
        <section>
            <h2>Thank you</h2>
            <img src="img/thanks.jpg" alt="T.Hanks">
        </section>
    </div>
</div>

<script src="reveal.js-3.5.0/lib/js/head.min.js"></script>
<script src="reveal.js-3.5.0/js/reveal.js"></script>

<script>
    // More info about config & dependencies:
    // - https://github.com/hakimel/reveal.js#configuration
    // - https://github.com/hakimel/reveal.js#dependencies
    Reveal.initialize({
        dependencies: [
            { src: 'reveal.js-3.5.0/plugin/markdown/marked.js' },
            { src: 'reveal.js-3.5.0/plugin/markdown/markdown.js' },
            { src: 'reveal.js-3.5.0/plugin/notes/notes.js', async: true },
            { src: 'reveal.js-3.5.0/plugin/highlight/highlight.js', async: true, callback: function() {
                    hljs.configure({languages: ['php']});
                    hljs.initHighlightingOnLoad();
                }
            }
        ]
    });
</script>
</body>
</html>
