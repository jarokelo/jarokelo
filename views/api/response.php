<h2 id="response">Response</h2>

The response is always in JSON. If the <code>success</code> key is true, than the request has been
successful, otherwise something happened.

Error example:
<pre><code class="json">{
    "success": false,
    "data": {
        "name": "Not Found",
        "message": "City not found or inactive.",
        "code": 0,
        "status": 404,
        "type": "yii\\web\\HttpException"
    }
}</code></pre>