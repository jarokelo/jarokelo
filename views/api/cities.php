<h2 id="cities">Cities</h2>

<h3 id="cities-index">List cities</h3>
<p>Returns the available cities ordered by name. The list is paginated, showing 20 item per page by default.</p>

<table class="table table-bordered">
    <tr>
        <th>URL</th>
        <td>/api/v2/cities</td>
    </tr>
    <tr>
        <th>Input parameters</th>
        <td>N/A</td>
    </tr>
</table>

<h4>Result</h4>

<p>The <code>items</code> node contains the items, and the <code>pagination</code> contains the URLs for the pagination.</p>

<table class="table table-bordered">
    <tr>
        <th>id</th>
        <td>ID of the city</td>
    </tr>
    <tr>
        <th>name</th>
        <td>Name of the city</td>
    </tr>
    <tr>
        <th>has_districts</th>
        <td>0 = the city doesn't have districts<br>
            1 = the city has districts</td>
    </tr>
    <tr>
        <th>latitude</th>
        <td>latitude of the city central, default is 0.00000000</td>
    </tr>
    <tr>
        <th>longitude</th>
        <td>longitude of the city central, default is 0.00000000</td>
    </tr>
    <tr>
        <th>url</th>
        <td>API endpoint URL for getting the city details</td>
    </tr>
    <tr>
        <th>reports</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>count</th>
                    <td>Number of reports in the city</td>
                </tr>
                <tr>
                    <th>url</th>
                    <td>API endpoint URL for listing the reports in the city</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<h3 id="cities-view">City details</h3>
<p>Returns the details of the city.</p>

<table class="table table-bordered">
    <tr>
        <th>URL</th>
        <td>/api/cities/$id</td>
    </tr>
    <tr>
        <th>Input parameter (as part of the URL)</th>
        <td>City ID</td>
    </tr>
    <tr>
        <th>Input parameters (GET)</th>
        <td>
            <p>All parameters are optional.</p>
            <table class="table table-bordered">
                <tr>
                    <th>limit</th>
                    <td>Set the number of items in the result. Minimum value: 1, maximum value: 20.</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<h4>Result</h4>

<p>The <code>items</code> node contains the items, and the <code>pagination</code> contains the URLs for the pagination.</p>

<table class="table table-bordered">
    <tr>
        <th>id</th>
        <td>ID of the city</td>
    </tr>
    <tr>
        <th>name</th>
        <td>Name of the city</td>
    </tr>
    <tr>
        <th>has_districts</th>
        <td>0 = the city doesn't have districts<br>
            1 = the city has districts</td>
    </tr>
    <tr>
        <th>latitude</th>
        <td>latitude of the city central, default is 0.00000000</td>
    </tr>
    <tr>
        <th>longitude</th>
        <td>longitude of the city central, default is 0.00000000</td>
    </tr>
    <tr>
        <th>url</th>
        <td>API endpoint URL for getting the city details (this page)</td>
    </tr>
    <tr>
        <th>reports</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>count</th>
                    <td>Number of reports in the city</td>
                </tr>
                <tr>
                    <th>url</th>
                    <td>API endpoint URL for listing the reports in the city</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <th>districts</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>reports</th>
                    <td>
                        <table class="table table-bordered">
                            <tr>
                                <th>count</th>
                                <td>Number of reports in the district</td>
                            </tr>
                            <tr>
                                <th>url</th>
                                <td>API endpoint URL for listing the reports in the district</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<h4>Error</h4>

<p>Exception 404 "Not Found" is thrown if the report is not found or unavailable to show.</p>

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