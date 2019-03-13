import axios from "axios";
import AuthService from "~/pages/index/services/AuthService";

export default function authGuard(to, from, next) {
  AuthService.init()
  if (AuthService.isGuest()) {
    next("/login");
  } else {

    fetchUser({ id: AuthService.tokens.uid }).then(
      ({ data }) => {
        if (data.result) {
          return redirectToPersonal(next, data.result);
        }
      }
    );
  }
}

function fetchUser(params) {
  const payload = {
    params: { id: params.id }
  };


  return axios({
    method: 'post',
    url: '/v1/user',
    data: payload,
    headers: {
      Authorization: `Bearer ${AuthService.tokens.access}`
    }
  });

}

function redirectToPersonal(next, data) {
  const user = data.user;

  return next(vm => {
    vm.$store.commit("SET_USER", user);

    if (user.socketUrl) {
      socketFactory(1,
        user.socketUrl,
        socket => {
          vm.ws = socket;
        },
        (data) => {
          console.log(data);
        });
    }

  });
}

function socketFactory(attempt, url, handler, messageCallback) {

  const maxAttempts = 5;

  if (attempt <= maxAttempts) {
    let socket;

    try {
      socket = new window.WebSocket(url);
    } catch (e) {
      setTimeout(() => socketFactory(attempt + 1, url, handler, messageCallback), 3000);
    }

    socket.onerror = function (e) {
      console.error(e);

      setTimeout(() => socketFactory(attempt + 1, url, handler, messageCallback), 3000);
    };

    socket.onmessage = function (message) {
      try {
        const data = JSON.parse(message.data);
        console.log(data);
        messageCallback(data.message);
      } catch (e) {
        console.error(e)
      }
    };

    handler(socket);
  } else {
    setTimeout(() => window.location.reload(), 10000);

    throw new Error(`After ${maxAttempts} attempts, could not connect to websocket "${url}".`);
  }
}
