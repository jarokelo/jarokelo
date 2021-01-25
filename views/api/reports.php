<h2 id="reports">Reports</h2>

<h3 id="reports-index">List reports</h3>
<p>Returns the available reports ordered by last modified time. The list is paginated, showing 20 item per page by default.</p>

<table class="table table-bordered">
    <tr>
        <th>URL</th>
        <td>/api/v2/reports</td>
    </tr>
    <tr>
        <th>Input parameters (GET)</th>
        <td>
            <p>All parameters are optional.</p>
            <table class="table table-bordered">
                <tr>
                    <th>city</th>
                    <td>Filter the list by City ID.</td>
                </tr>
                <tr>
                    <th>district</th>
                    <td>Filters the list by District ID.</td>
                </tr>
                <tr>
                    <th>term</th>
                    <td>Search in the names of the reports.</td>
                </tr>
                <tr>
                    <th>user</th>
                    <td>Filters the list by User ID.</td>
                </tr>
                <tr>
                    <th>status</th>
                    <td>Filters the list by Status ID.</td>
                </tr>
                <tr>
                    <th>near</th>
                    <td>Filters the list to the nearest reports. The parameter has to be a valid latitude, longitude coordinate separated by comma. For example: <code>near=47.17291127826699,19.34349060058593</code></td>
                </tr>
                <tr>
                    <th>mylocation</th>
                    <td>If set, then the list will contain the reports' distance from the user. The parameter has to be a valid latitude, longitude coordinate separated by comma. For example: <code>mylocation=47.17291127826699,19.34349060058593</code></td>
                </tr>
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
        <td>ID</td>
    </tr>
    <tr>
        <th>title</th>
        <td>Report name</td>
    </tr>
    <tr>
        <th>image</th>
        <td>Image URL of the report</td>
    </tr>
    <tr>
        <th>created</th>
        <td>UNIX timestamp of the time the report was created</td>
    </tr>
    <tr>
        <th>updated</th>
        <td>UNIX timestamp of the time the report was updated</td>
    </tr>
    <tr>
        <th>user</th>
        <td>Full name of the user. If they wanted to stay anonymous, the value is simply "Anonymous" (localized)</td>
    </tr>
    <tr>
        <th>category</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>id</th>
                    <td>Category ID</td>
                </tr>
                <tr>
                    <th>name</th>
                    <td>Category name</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <th>institution</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>id</th>
                    <td>Responsible institution ID. Null if not set.</td>
                </tr>
                <tr>
                    <th>name</th>
                    <td>Responsible institution name. Null if not set.</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <th>status</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>id</th>
                    <td>ID of the status</td>
                </tr>
                <tr>
                    <th>name</th>
                    <td>Name of the status</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <th>address</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>full_address</th>
                    <td>Full address of the report</td>
                </tr>
                <tr>
                    <th>latitude</th>
                    <td>latitude</td>
                </tr>
                <tr>
                    <th>longitude</th>
                    <td>longitude</td>
                </tr>
                <tr>
                    <th>zoom</th>
                    <td>value is constant <?=\app\modules\api\controllers\SubmitController::ZOOM_VALUE?></td>
                </tr>
                <tr>
                    <th>city</th>
                    <td>
                        <table class="table table-bordered">
                            <tr>
                                <th>id</th>
                                <td>The ID of the city</td>
                            </tr>
                            <tr>
                                <th>name</th>
                                <td>The name of the city</td>
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
                                <td>The ID of the street's district. Null if not set.</td>
                            </tr>
                            <tr>
                                <th>name</th>
                                <td>The name of the street's district. Null if not set.</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>street_name</th>
                    <td>Street name</td>
                </tr>
                <tr>
                    <th>post_code</th>
                    <td>Postal code</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <th>url</th>
        <td>API endpoint URL of the report details</td>
    </tr>
    <tr>
        <th>distance</th>
        <td>Distance of the user from the report, in meters. Set only if the "near" or the "mylocation" parameters are available.</td>
    </tr>
</table>

<h3 id="reports-view">Report details</h3>
<p>Returns the details of the report.</p>

<table class="table table-bordered">
    <tr>
        <th>URL</th>
        <td>/api/v2/reports/$id</td>
    </tr>
    <tr>
        <th>Input parameter (as part of the URL)</th>
        <td>Report ID</td>
    </tr>
</table>

<h4>Result</h4>

<table class="table table-bordered">
    <tr>
        <th colspan="2">ℹ️ The result contains all the fields as described in the previous chapter, extended with the following ones:</th>
    </tr>
    <tr>
        <th>description</th>
        <td>Report description (HTML formatted).</td>
    </tr>
    <tr>
        <th>media[]</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>image</th>
                    <td>
                        Thumbnail image URL if <code>type=image</code><br>
                        Youtube video thumbnail image URL if <code>type=video</code>
                    </td>
                </tr>
                <tr>
                    <th>url</th>
                    <td>
                        Large resolution image URL if <code>type=image</code><br>
                        Youtube video URL if <code>type=video</code>
                    </td>
                </tr>
                <tr>
                    <th>type</th>
                    <td>"image" or "video" (video means Youtube)</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <th>activity[]</th>
        <td>
            <table class="table table-bordered">
                <tr>
                    <th>id</th>
                    <td>ID of the activity</td>
                </tr>
                <tr>
                    <th>user</th>
                    <td>User's or insitution's name</td>
                </tr>
                <tr>
                    <th>type</th>
                    <td>"answer" (by institution) or "comment" (by user)</td>
                </tr>
                <tr>
                    <th>created_at</th>
                    <td>UNIX timestamp</td>
                </tr>
                <tr>
                    <th>user</th>
                    <td>Name of the user</td>
                </tr>
                <tr>
                    <th>comment</th>
                    <td>Comment message (HTML formatted) if <code>type=comment</code></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<h4>Error</h4>

<p>Exception 404 "Not Found" is thrown if the report is not found, inactive or unavailable to show.</p>

<pre><code class="json">{
    "success": false,
    "data": {
        "name": "Not Found",
        "message": "Report not found or inactive.",
        "code": 0,
        "status": 404,
        "type": "yii\\web\\HttpException"
    }
}</code></pre>