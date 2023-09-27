@extends('admin.layouts.app')

@section('panel')


   
   <div>
  <label for="upline-users">Number of users in upline:</label>
  <input type="number" id="upline-users" name="upline-users">
</div>

<div>
  <label for="downline-users">Number of users in downline:</label>
  <input type="number" id="downline-users" name="downline-users">
</div>

<button onclick="calculatePairs()">Calculate</button>

<p id="result"></p>


@endsection




@push('script')
    <script>
		function calculatePairs() {
  const uplineUsers = parseInt(document.getElementById("upline-users").value);
  const downlineUsers = parseInt(document.getElementById("downline-users").value);

  // Calculate pairs in upline
  let uplinePairs = 0;
  for (let i = 1; i <= 11; i++) {
    const levelUsers = Math.pow(2, i-1);
    const levelPairs = levelUsers * (i-1);
    uplinePairs += levelPairs;
  }

  // Calculate pairs in downline
  let downlinePairs = 0;
  for (let i = 1; i <= 11; i++) {
    const levelUsers = Math.pow(2, i-1);
    const levelPairs = levelUsers * (i-1);
    downlinePairs += levelPairs * (downlineUsers - uplineUsers);
  }

  const totalPairs = uplinePairs + downlinePairs;

  const result = `The total number of pairs in the upline and downline would be ${totalPairs.toLocaleString()} pairs. (Upline: ${uplinePairs.toLocaleString()} pairs, Downline: ${downlinePairs.toLocaleString()} pairs)`;

  document.getElementById("result").innerText = result;
}

    </script>

@endpush