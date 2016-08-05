@extends('layouts.app')

@section('content')
<style>
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
</style>

<!--
SKELETON
<tr>
	<th>Route</th>
	<th>Method</th>
	<th>
		Preconditions
	</th>
	<th>
		Postconditions
	</th>
	<th>
		Options
	</th>
</tr>
-->

<center>
	<table style="width:90%">
		<!-- Headers -->
		<tr>
			<th>Route</th>
			<th>Method</th>
			<th>Preconditions</th>
			<th>Postconditions</th>
			<th>Options</th>
		</tr>

		<!-- Authentication -->
		<tr>
			<th>api/register</th>
			<th>POST</th>
			<th>
				Request must contain an email and a password. <br>
				The password must be confirmed in a password_confirmation field.
			</th>
			<th>
				Registers the specified user. <br>
				Returns 400 upon failure to meet preconditions. <br>
				DOES NOT AUTHENTICATE THE USER.
			</th>
			<th>
				N/A
			</th>
		</tr>
		<tr>
			<th>api/authenticate</th>
			<th>POST</th>
			<th>
				Request must contain an email and a password.
			</th>
			<th>
				Returns a token if the credential or valid. If not, returns 401.
			</th>
			<th>
				N/A
			</th>
		</tr>
		<tr>
			<th>api/refreshToken</th>
			<th>POST</th>
			<th>
				Request must contain a token in the authorization header <br> 
				created within 2 weeks.
			</th>
			<th>
				Returns a new token for the user of the previous token.
			</th>
			<th>
				N/A
			</th>
		</tr>
		<tr>
			<th>api/destroyToken</th>
			<th>POST</th>
			<th>
				Request must contain a token in the authorization header <br>
				created within 60 minutes.
			</th>
			<th>
				Puts the token on the blacklist so it may no longer be used
			</th>
			<th>
				N/A
			</th>
		</tr>

		<!-- Geolocations -->
		<tr>
			<th>api/geolocations</th>
			<th>GET</th>
			<th>
				N/A
			</th>
			<th>
				Returns the first 50 geolocations found.
			</th>
			<th>
				Request may specify a radius to search within by containing <br>
				a center's latitude, longitude and a radius. One may change <br>
				the unit for the radius by including a unit variable with <br>
				m for miles, k for kilometers, or n for nautical miles. <br>
				Request may also search for name with a name variable <br>
				or a location type by including a locationType variable.
			</th>
		</tr>
	</table>
</center>
@endsection