@extends('docs.index')

@section('content')
<h1 class="text-3xl font-bold mb-8">Developer Setup</h1>
<p>This guide provides detailed instructions for setting up the AIHMS-vbeta development environment.</p>

<h2 class="text-2xl font-bold mt-8 mb-4">Prerequisites</h2>
<p>Before you begin, ensure you have the following installed on your system:</p>
<ul class="list-disc list-inside">
  <li>PHP >= 8.2</li>
  <li>Composer</li>
  <li>Node.js & npm</li>
  <li>A database server (MySQL, PostgreSQL, or SQLite).</li>
</ul>

<h2 class="text-2xl font-bold mt-8 mb-4">1. Clone the Repository</h2>
<p>First, clone the project repository to your local machine.</p>
<pre><code class="language-bash">
git clone https://github.com/jesse457/AIHMS-vbeta.git
cd AIHMS-vbeta
</code></pre>

<h2 class="text-2xl font-bold mt-8 mb-4">2. Install Dependencies</h2>
<p>Install the backend and frontend dependencies using Composer and npm.</p>
<pre><code class="language-bash">
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
</code></pre>

<h2 class="text-2xl font-bold mt-8 mb-4">3. Environment Configuration</h2>
<p>Create your local environment file by copying the example file.</p>
<pre><code class="language-bash">
cp .env.example .env
</code></pre>
<p>Now, generate the application key:</p>
<pre><code class="language-bash">
php artisan key:generate
</code></pre>

<h3 class="text-xl font-bold mt-8 mb-4">Configure <code>.env</code> variables</h3>
<p>Open the <code>.env</code> file and configure the following variables according to your local setup:</p>
<ul class="list-disc list-inside">
  <li><strong>Database Connection:</strong>
    <pre><code class="language-env">
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aihms
DB_USERNAME=root
DB_PASSWORD=
    </code></pre>
  </li>
  <li><strong>Application URL:</strong>
    <pre><code class="language-env">
APP_URL=http://aihms.test
    </code></pre>
  </li>
  <li><strong>Mail Configuration:</strong> (For development, you can use the <code>log</code> driver)
    <pre><code class="language-env">
MAIL_MAILER=log
    </code></pre>
  </li>
  <li><strong>Queue Connection:</strong> (For development, <code>sync</code> is simplest, but <code>database</code> is also a good option)
    <pre><code class="language-env">
QUEUE_CONNECTION=sync
    </code></pre>
  </li>
</ul>

<h2 class="text-2xl font-bold mt-8 mb-4">4. Run Migrations and Seeders</h2>
<p>Set up the database schema and populate it with initial data. This will create the necessary tables for the central application and the tenant-specific data.</p>
<pre><code class="language-bash">
php artisan migrate --seed
</code></pre>

<h2 class="text-2xl font-bold mt-8 mb-4">5. Build Frontend Assets</h2>
<p>Compile the frontend assets using Vite.</p>
<pre><code class="language-bash">
# Run the development server with hot-reloading
npm run dev
</code></pre>

<h2 class="text-2xl font-bold mt-8 mb-4">6. Start the Development Server</h2>
<p>Finally, start the Laravel development server. You can use the built-in <code>serve</code> command or the concurrent script provided in <code>composer.json</code> for a more comprehensive development experience.</p>
<p><strong>Simple Method:</strong></p>
<pre><code class="language-bash">
php artisan serve
</code></pre>
<p>The application will be available at <code>http://localhost:8000</code>.</p>
<p><strong>Advanced Method (Recommended):</strong></p>
<p>This command, defined in <code>composer.json</code>, concurrently runs the PHP server, queue worker, log pail, and Vite server.</p>
<pre><code class="language-bash">
composer run dev
</code></pre>

<h2 class="text-2xl font-bold mt-8 mb-4">7. Creating a Tenant</h2>
<p>To use the application, you need to create a tenant. You can do this through the artisan tinker shell:</p>
<pre><code class="language-bash">
php artisan tinker

# Inside tinker
$tenant = App\Models\Tenant::create(['id' => 'hospital']);
$tenant->domains()->create(['domain' => 'hospital.localhost']);
</code></pre>
<p>After creating the tenant, you need to add the domain to your <code>/etc/hosts</code> file:</p>
<pre><code>
127.0.0.1   hospital.localhost
</code></pre>
<p>Now you can access the tenant application at <code>http://hospital.localhost:8000</code>.</p>
@endsection
