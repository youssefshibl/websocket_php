let join = true;
let channel_name = "channel_" + Math.floor(Math.random() * 100) + 1;
let socket = new WebSocket("ws://127.0.0.1:9001/");

socket.onopen = function (e) {
  alert("[open] Connection established");
  let channel_message = {
    type: "config",
    number: 1,
    channel: channel_name,
  };
  socket.send(JSON.stringify(channel_message));
};

socket.onmessage = function (event) {
  console.log(`[message] Data received from server: ${event.data}`);
  let data_json = JSON.parse(event.data);
  if ((data_json.type = "message")) {
    let box_message_ele = document.querySelector(".messagebox");
    let text_message = `<div class="servermessage">
                         <span>${data_json.from}</span>
                         <span>${data_json.message}</span>
                        </div>`;
    box_message_ele.innerHTML += text_message;
  }
};

socket.onclose = function (event) {
  if (event.wasClean) {
    console.log(
      `[close] Connection closed cleanly, code=${event.code} reason=${event.reason}`
    );
  } else {
    // e.g. server process killed or network down
    // event.code is usually 1006 in this case
    console.log("[close] Connection died");
  }
};

socket.onerror = function (error) {
  console.log(`[error]`);
};
window.onbeforeunload = function (event) {
  socket.close();
};

let send_button = document.querySelector(".send_button");
send_button.onclick = function () {
  let send_value = document.querySelector('[name="message"]').value;
  let send_channel = document.querySelector('[name="channel_name"]').value;
  if (send_value && join && send_channel) {
    let send_message = {
      type: "message",
      from: channel_name,
      to: send_channel,
      message: send_value,
    };
    socket.send(JSON.stringify(send_message));
    document.querySelector('[name="message"]').value = "";
  } else {
    alert("you should join or enter string or enter channel");
  }
};

document.querySelector(".channel_name").innerHTML = channel_name;

