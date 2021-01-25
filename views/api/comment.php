<h2 id="comment">Comment</h2>

<p>Submits a comment from the user to a report.</p>


<table class="table table-bordered">
    <tr>
        <th>URL</th>
        <td>/api/v2/comment</td>
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
                    <th>report_id</th>
                    <td>The ID of the report</td>
                </tr>
                <tr>
                    <th>message</th>
                    <td>The body of the comment</td>
                </tr>
                <tr>
                    <th>image <i>[optional]</i></th>
                    <td>An image resource sent with the comment</td>
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
    "message": "Invalid report id",
    "code": 0,
    "status": 400,
    "type": "yii\\web\\HttpException"
  }
}</code></pre>
