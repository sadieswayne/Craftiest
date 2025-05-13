<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Invoice</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			background-color: #f9f9f9;
			margin: 0;
			padding: 0;
		}
		.container {
			width: 80%;
			margin: 20px auto;
			background: #fff;
			padding: 20px;
			border-radius: 8px;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		}
		.header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			border-bottom: 2px solid #ddd;
			padding-bottom: 10px;
			margin-bottom: 20px;
		}
		.invoice-details {
			text-align: right;
		}
		.section {
			margin-bottom: 20px;
		}
		.table {
			width: 100%;
			border-collapse: collapse;
		}
		.table th, .table td {
			border: 1px solid #ddd;
			padding: 8px;
			text-align: left;
		}
		.table th {
			background: #f1f1f1;
		}
		.totals {
			width: 50%;
			float: right;
			margin-top: 20px;
		}
		.totals td {
			padding: 8px;
			border: 1px solid #ddd;
		}
		.totals .total-due {
			font-size: 1.2em;
			font-weight: bold;
			background: #f1f1f1;
		}
		.footer {
			text-align: center;
			font-size: 12px;
			color: #777;
			margin-top: 200px;
			clear: both;
		}
	</style>
</head>
<body>
<div class="container">
	<div class="header">
		<div class="app-name">
			<h1>{{ $setting->site_name }}</h1>
		</div>
		<div class="invoice-details">
			<h2>INVOICE</h2>
			<p>Order Ref: <strong>{{ strtoupper($invoice->order_id) }}</strong></p>
			<p>Order Date/Time: <strong>{{ date('j.n.Y - H:i:s', strtotime($invoice->created_at)) }}</strong></p>
		</div>
	</div>

	<div class="section">
		<h3>Client Details</h3>
		<p>{{ $invoice->user?->fullName() }}</p>
		<p>{{ $invoice->user->email }}</p>
		<p>{{ $invoice->user->phone }}</p>
		<p>{{ $invoice->user->address }}</p>
		@if($invoice->user->postal || $invoice->user->state || $invoice->user->city || $invoice->user->country)
			<p>{{ $invoice->user->postal ? $invoice->user->postal . ', ' : '' }}
			{{ $invoice->user->state ? $invoice->user->state . ', ' : '' }}
			{{ $invoice->user->city ? $invoice->user->city . ', ' : '' }}
			{{ $invoice->user->country ?? '' }}</p>
		@else
			<p>{{__('Not Available')}}</p>
		@endif

	</div>

	<div class="section">
		<h3>{{ $setting->invoice_name }}</h3>
		<p>{{ $setting->invoice_address }}, {{ $setting->invoice_city }}, {{ $setting->invoice_state }}</p>
		<p>{{ $setting->invoice_country }}, {{ $setting->invoice_postal }}</p>
		<p>{{ $setting->invoice_website }}</p>
		<p>{{ $setting->invoice_phone }}</p>
	</div>

	<table class="table">
		<thead>
		<tr>
			<th>#</th>
			<th>Product</th>
			<th>Quantity</th>
			<th>Unit Price</th>
			<th>Total</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>1</td>
			<td><strong>{{ @$invoice->plan->name ?? 'Archived Plan' }}</strong></td>
			<td>1</td>
			<td>{{ currency()->symbol }}{{ $invoice->price }}</td>
			<td>{{ currency()->symbol }}{{ $invoice->price }}</td>
		</tr>
		</tbody>
	</table>

	<table class="totals">
		<tr>
			<td>Subtotal</td>
			<td>{{ currency()->symbol }}{{ $invoice->price }}</td>
		</tr>
		<tr>
			<td>Vat Rate</td>
			<td>{{ $invoice->tax_rate }}%</td>
		</tr>
		<tr>
			<td>Vat Due</td>
			<td>{{ isset($invoice->tax_rate) && $invoice->tax_rate > 0 ? currency()->symbol . $invoice->tax_value : '-' }}</td>
		</tr>
		<tr class="total-due">
			<td>Total Due</td>
			<td>{{ currency()->symbol }}{{ $invoice->price }}</td>
		</tr>
	</table>

	<div class="footer">
		<p>{{__('Thank you very much for doing business with us. We look forward to working with you again!')}}</p>
	</div>
</div>
</body>
</html>
