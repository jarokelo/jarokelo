<h3 id="cities-streets">List streets of a city</h3>
<p>Returns the available streets of the given city, ordered by street name ascending. The list is paginated, showing 20 item per page by default.</p>

<table class="table table-bordered">
    <tr>
        <th>URL</th>
        <td>/api/v2/streets/$id</td>
    </tr>
    <tr>
        <th>Input parameters (as part of the URL)</th>
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
        <td>ID of the street</td>
    </tr>
    <tr>
        <th>name</th>
        <td>Name of the street</td>
    </tr>
    <tr>
        <th>latitude</th>
        <td>latitude of the street central, default is 0.00000000</td>
    </tr>
    <tr>
        <th>longitude</th>
        <td>longitude of the street central, default is 0.00000000</td>
    </tr>
    <tr>
        <th>district</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>id</th>
                    <td>The ID of the street's district</td>
                </tr>
                <tr>
                    <th>name</th>
                    <td>The name of the street's district</td>
                </tr>
            </table>
        </td>
    </tr>
</table>