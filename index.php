<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>P2P Video Call</title>
    <script src="https://cdn.jsdelivr.net/npm/peerjs@1.3.2/dist/peerjs.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col items-center p-5">
    <h1 class="text-3xl font-bold mb-4">P2P Video Calling</h1>

    <p class="text-lg mb-2">Your Peer ID:
        <span id="peerId" class="text-blue-600 font-mono">Generating...</span>
        <button id="copyPeerId" class="ml-2 bg-gray-200 text-sm px-2 py-1 rounded hover:bg-gray-300">Copy</button>
    </p>
    <script>
        document.getElementById('copyPeerId').addEventListener('click', () => {
            const peerId = document.getElementById('peerId').innerText;
            navigator.clipboard.writeText(peerId).then(() => {
                alert('Peer ID copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy Peer ID: ', err);
            });
        });
    </script>

    <div class="flex gap-2 mb-4">
        <input type="text" id="remoteId" placeholder="Enter remote ID"
            class="w-64 p-2 border border-gray-300 rounded" />
        <button id="connect" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Call</button>
    </div>

    <p id="status" class="text-gray-700 mb-4">Status: Not Connected</p>

    <div class="flex gap-4">
        <div>
            <h2 class="text-lg font-semibold mb-2">Your Video</h2>
            <video id="localVideo" autoplay muted class="w-64 h-48 bg-black rounded"></video>
        </div>
        <div>
            <h2 class="text-lg font-semibold mb-2">Remote Video</h2>
            <video id="remoteVideo" autoplay class="w-64 h-48 bg-black rounded"></video>
        </div>
    </div>

    <script>
        const peer = new Peer({
            host: '0.peerjs.com',
            port: 443,
            path: '/',
            secure: true
        });

        const localVideo = document.getElementById('localVideo');
        const remoteVideo = document.getElementById('remoteVideo');
        const peerIdDisplay = document.getElementById('peerId');
        const connectButton = document.getElementById('connect');
        const remoteIdInput = document.getElementById('remoteId');
        const status = document.getElementById('status');

        let localStream;

        // Get media stream
        navigator.mediaDevices.getUserMedia({ video: true, audio: true })
            .then(stream => {
                localStream = stream;
                localVideo.srcObject = stream;
            })
            .catch(err => {
                alert('Error accessing camera/mic: ' + err);
            });

        peer.on('open', id => {
            peerIdDisplay.innerText = id;
        });

        // Receiving call
        peer.on('call', call => {
            call.answer(localStream); // Answer the call with your own video stream
            call.on('stream', remoteStream => {
                remoteVideo.srcObject = remoteStream;
                status.innerText = 'Status: Connected';
            });
        });

        // Make call
        connectButton.addEventListener('click', () => {
            const remoteId = remoteIdInput.value;
            if (!remoteId) return alert('Please enter a remote peer ID');

            const call = peer.call(remoteId, localStream);

            call.on('stream', remoteStream => {
                remoteVideo.srcObject = remoteStream;
                status.innerText = 'Status: Connected';
            });

            call.on('close', () => {
                status.innerText = 'Status: Call Ended';
            });

            call.on('error', err => {
                console.error(err);
                alert('Call error');
            });
        });
    </script>
</body>

<footer class="mt-4 text-gray-600 text-sm">
    <p>Created with ❤️ by Your Tarikul</p>

</html>