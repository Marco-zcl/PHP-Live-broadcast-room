<!DOCTYPE html>
<html lang="zh">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>2tina 直播间</title>
  <script src="https://cdn.jsdelivr.net/npm/flv.js/dist/flv.min.js"></script>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Helvetica, Arial, sans-serif;
      background-color: #fafafa;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

  .live-container {
      background-color: #fff;
      border-radius: 16px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 90%;
      max-width: 1200px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

  .video-and-danmu {
      display: flex;
      height: 550px;
    }

  .video-wrapper {
      flex: 2;
      position: relative;
    }

    #videoElement {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: #000;
    }

  .danmu-container {
      flex: 1;
      background-color: rgba(0, 0, 0, 0.03);
      overflow-y: auto;
      padding: 15px;
      display: flex;
      flex-direction: column-reverse;
    }

  .danmu-item {
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 10px;
      padding: 12px 18px;
      margin-bottom: 12px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      font-size: 15px;
      color: #333;
    }

  .controls {
      padding: 20px;
      text-align: center;
      background-color: rgba(245, 245, 245, 0.9);
    }

    #playButton {
      background-color: #007AFF;
      border: none;
      color: #fff;
      padding: 14px 28px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      margin: 6px 2px;
      cursor: pointer;
      border-radius: 12px;
      transition: all 0.3s ease;
    }

    #playButton:hover {
      background-color: #0056B3;
      transform: scale(1.05);
    }

  .status {
      margin-top: 12px;
      font-style: italic;
      color: #888;
    }

  .live-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 18px;
      border-bottom: 1px solid #E8E8E8;
      background-color: #f5f5f5;
    }

  .live-title {
      font-size: 20px;
      font-weight: 600;
      color: #222;
    }

    #liveViewers {
      background-color: rgba(0, 0, 0, 0.05);
      padding: 10px 16px;
      border-radius: 12px;
      font-size: 15px;
      color: #666;
    }

  .danmu-input {
      display: flex;
      padding: 16px;
      background-color: #f0f0f0;
      border-radius: 12px;
    }

    #usernameInput,
    #danmuInput {
      flex: 1;
      padding: 10px;
      margin-right: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    #sendDanmu {
      background-color: #FF5722;
      color: #fff;
      border: none;
      padding: 10px 16px;
      border-radius: 8px;
      cursor: pointer;
    }

    /* 新增视频声音调节部分的样式 */
  .volume-control {
      width: 120px;
      -webkit-appearance: none;
      background: #f0f0f0;
      height: 6px;
      border-radius: 3px;
      outline: none;
      opacity: 0.7;
      transition: opacity 0.2s;
    }

  .volume-control:hover {
      opacity: 1;
    }

  .volume-control::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: #007AFF;
      cursor: pointer;
    }

  .volume-control::-moz-range-thumb {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: #007AFF;
      cursor: pointer;
    }

    /* 新增直播中标识的样式 */
  .live-indicator {
      position: absolute;
      top: 12px;
      left: 12px;
      background-color: #007AFF;
      color: #fff;
      padding: 8px 12px;
      border-radius: 10px;
      font-size: 14px;
    }
  </style>
</head>

<body>
  <div class="live-container">
    <div class="live-info">
      <div class="live-title">2tina 直播间</div>
      <div id="liveViewers">观看人数: 0</div>
    </div>
    <div class="video-and-danmu">
      <div class="video-wrapper">
        <video id="videoElement"></video>
        <div class="live-indicator">直播中</div> <!-- 新增直播中标识 -->
      </div>
      <div class="danmu-container" id="danmuContainer"></div>
    </div>
    <div class="danmu-input">
      <input type="text" id="usernameInput" placeholder="输入您的名字">
      <input type="text" id="danmuInput" placeholder="输入弹幕">
      <button id="sendDanmu">发送</button>
    </div>
    <div class="controls">
      <button id="playButton">开始观看</button>
      <div class="status" id="statusMessage"></div>
      <input type="range" class="volume-control" min="0" max="1" step="0.1" /> <!-- 新增声音调节滑块 -->
    </div>
  </div>

  <script>
    var videoElement = document.getElementById('videoElement');
    var playButton = document.getElementById('playButton');
    var statusMessage = document.getElementById('statusMessage');
    var liveViewers = document.getElementById('liveViewers');
    var danmuContainer = document.getElementById('danmuContainer');
    var usernameInput = document.getElementById('usernameInput');
    var danmuInput = document.getElementById('danmuInput');
    var sendDanmuButton = document.getElementById('sendDanmu');

    // 更新在线人数
    function updateViewers() {
      fetch('update_viewers.php')
   .then(response => response.text())
   .then(data => {
          liveViewers.textContent = `观看人数: ${data}`;
        });
    }
    setInterval(updateViewers, 5000);

    // 发送弹幕
    sendDanmuButton.onclick = function() {
      let username = usernameInput.value.trim();
      let danmuText = danmuInput.value.trim();
      if (username && danmuText) {
        fetch('send_danmu.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `username=${encodeURIComponent(username)}&danmu=${encodeURIComponent(danmuText)}`
        })
   .then(() => {
          danmuInput.value = '';
          getDanmu();
        });
      }
    };

    // 获取最新弹幕
    function getDanmu() {
      fetch('get_danmu.php')
   .then(response => response.json())
   .then(data => {
          danmuContainer.innerHTML = '';
          data.forEach(item => {
            let danmuElement = document.createElement('div');
            danmuElement.className = 'danmu-item';
            danmuElement.textContent = `${item.username}: ${item.danmu}`;
            danmuContainer.prepend(danmuElement);
          });
        });
    }
    setInterval(getDanmu, 1000);

    if (flvjs.isSupported()) {
      var flvPlayer = flvjs.createPlayer({
        type: 'flv',
        url: 'https://obs.2tina.top/live/kfiefe.flv'
      });
      flvPlayer.attachMediaElement(videoElement);
      flvPlayer.load();

      playButton.onclick = function() {
        videoElement.play().then(() => {
          playButton.style.display = 'none';
          statusMessage.textContent = '直播正在进行中...';
        }).catch(error => {
          statusMessage.textContent = '播放失败，请检查您的网络连接';
        });
      };

      // 尝试自动播放
      videoElement.play().then(() => {
        playButton.style.display = 'none';
        statusMessage.textContent = '直播正在进行中...';
      }).catch(error => {
        statusMessage.textContent = '点击"开始观看"按钮加入直播';
      });
    } else {
      statusMessage.textContent = '您的浏览器不支持此直播，请尝试使用其他浏览器';
    }

    // 视频声音调节功能
    var volumeControl = document.querySelector('.volume-control');
    volumeControl.addEventListener('input', function() {
      videoElement.volume = this.value;
    });
  </script>
</body>

</html>