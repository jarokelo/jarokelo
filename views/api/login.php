<h2 id="login">Login</h2>

<p>Authenticate the user and get their profile.</p>

<table class="table table-bordered">
    <tr>
        <th>URL</th>
        <td>/api/v2/login</td>
    </tr>
    <tr>
        <th>Input parameters (POST)</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>email</th>
                    <td>Email of the user</td>
                </tr>
                <tr>
                    <th>password</th>
                    <td>Password of the user</td>
                </tr>
            </table>

        </td>
    </tr>
</table>

<h4>Result</h4>

<table class="table table-bordered">
    <tr>
        <th>id</th>
        <td>ID</td>
    </tr>
    <tr>
        <th>email</th>
        <td>Email</td>
    </tr>
    <tr>
        <th>api_token</th>
        <td>The API token that can be used for request where the user must be authenticated.</td>
    </tr>
    <tr>
        <th>fullname</th>
        <td>Name </td>
    </tr>
    <tr>
        <th>image</th>
        <td>Profile picture URL</td>
    </tr>
    <tr>
        <th>city</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>id</th>
                    <td>The ID of the user's city</td>
                </tr>
                <tr>
                    <th>name</th>
                    <td>The name of the user's city</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <th>district</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>id</th>
                    <td>The ID of the user's district</td>
                </tr>
                <tr>
                    <th>name</th>
                    <td>The name of the user's district</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <th>statistics</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>leaderboard</th>
                    <td>Global rank of the user</td>
                </tr>
                <tr>
                    <th>reports</th>
                    <td>Number of reports the user has submitted (regardless the status)</td>
                </tr>
                <tr>
                    <th>reports_solved</th>
                    <td>Number of reports the user has submitted and marked as solved</td>
                </tr>
                <tr>
                    <th>reports_unsolved</th>
                    <td>Number of reports the user has submitted and marked as unsolved</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<h4>Error</h4>

<p>Exception 401 "Unathorized" is thrown if the provided email and password credentials are invalid.</p>

<pre><code class="json">{
    "success": false,
    "data": {
        "name": "Unauthorized",
        "message": "Wrong username or password.",
        "code": 0,
        "status": 401,
        "type": "yii\\web\\HttpException"
    }
}</code></pre>
