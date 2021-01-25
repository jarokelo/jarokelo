<h2 id="submit">Submit report</h2>

<p>Submits a report from the user.</p>

<table class="table table-bordered">
    <tr>
        <th>URL</th>
        <td>/api/v2/submit</td>
    </tr>
    <tr>
        <th>Input parameters (POST)</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>api_token</th>
                    <td>The API token the login request gave back for the User.</td>
                </tr>
                <tr>
                    <th>city_id</th>
                    <td>The ID of the city</td>
                </tr>
                <tr>
                    <th>category_id</th>
                    <td>The ID of the Category</td>
                </tr>
                <tr>
                    <th>latitude</th>
                    <td>float number of the latitude</td>
                </tr>
                <tr>
                    <th>longitude</th>
                    <td>float number of the longitude</td>
                </tr>
                <tr>
                    <th>name</th>
                    <td>The report's name</td>
                </tr>
                <tr>
                    <th>description</th>
                    <td>The report's description</td>
                </tr>
                <tr>
                    <th>user_location</th>
                    <td>The complete string of the report's location. <code>Example: Budapest, Nádor u. 23, 1051 Magyarország</code></td>
                </tr>
                <tr>
                    <th>street_name</th>
                    <td>The name of the street without the house number <code>Example: Nádor utca</code></td>
                </tr>
                <tr>
                    <th>address</th>
                    <td>The name of the street with the house number <code>Example: Nádor utca 23</code></td>
                </tr>
                <tr>
                    <th>post_code</th>
                    <td>The post code from the address <code>Example: 1051</code></td>
                </tr>
                <tr>
                    <th>anonymous</th>
                    <td>Keep the reporter user's information private or not</td>
                </tr>
                <tr>
                    <th>image <i>[optional]</i></th>
                    <td>An image resource sent with the report</td>
                </tr>
            </table>

        </td>
    </tr>
</table>


<h4>Result</h4>

<p>The response will be a boolean true if the report has been saved successfully.</p>

<pre><code class="json">{
    "success": true,
    "data": true
}</code></pre>


<h4>Error</h4>

<p>When the request is done by a get method the response will be the following.</p>

<pre><code class="json">{
    "name": "Method Not Allowed",
    "message": "Method Not Allowed. This url can only handle the following request methods: POST, HEAD.",
    "code": 0,
    "status": 405,
    "type": "yii\\web\\HttpException"
}</code></pre>

<p>Exception 400 "Bad Request" is thrown if any of the required variables are not set or invalid.</p>

<pre><code class="json">{
  "success": false,
  "data": {
    "name": "Bad Request",
    "message": "Invalid latitude value",
    "code": 0,
    "status": 400,
    "type": "yii\\web\\HttpException"
  }
}</code></pre>
