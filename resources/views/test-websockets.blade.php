<head>
    <title>Pusher Test</title>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>

      // Enable pusher logging - don't include this in production
      Pusher.logToConsole = true;
      const token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiOTFlZmI1ZTE4Y2FmZGZmZjlmMTkwMGNlODVmNjFjMWQwNDA0ZTE1NDExYzBjMzI4OWZiOThmMGNkZTAyNTY0MDE5NGY1MjZhNmFiM2VjOTYiLCJpYXQiOjE2NjUwMzI1MzIuNDcxMzkxLCJuYmYiOjE2NjUwMzI1MzIuNDcxMzk4LCJleHAiOjE2OTY1Njg1MzIuMzg0NTkyLCJzdWIiOiI0MiIsInNjb3BlcyI6W119.E4O1JUiu7B9kCKE2KM6KY1jxOmkzGKfXo74mhvqUefiXnIxvOOhKqsb3-x-4SxgRqQlYEyzeaq7hY8cS0F5NWrOcXGxt4XyBFlc4jXq5HnzaBuR272bRxeHQjgGDA7XxVvxSh2mA54RwuCWU5EvomrKVsJ3ftk8gNBcjuw87bQHYBCAR9d2EaHk5224FX_zrBokDCsNHEJ486UVqxfJ7kUNrUiKLKxnEYXIGABu63DF51pbC_QfLChQzoUg98CxUNpRlSWAylCeg3BydttM0QmQR2GWZNzJjlQX9mlSV9q5Aq1NLLCnEgTjq72bVrwikVMz2La6ddxa7MsGoY5PQJBzWQAbYCoqX96tSRuchU5vNalKeEVDlhawa7i_D6x-rebxRqroEcWQjyfiIiprouRqAtj5TqcPJ0CLShwY3ak1e0eeBPAV_yr7pUAoinZl2MCRnWvoN0pdZSOtRUKp3ZsrIS2VscX9e4yNcbV4l7qhysBxBLHMXwGj4Ht8b9q77Qq2MpncFgQc08s8GxPAqbs-kRA9eZb6ueDSjmTH_ys5Alj4bwSbaXeS5qhwal_w6zRejsCbHiZicPg-EPj6WWWa_-kBrwXoGzWs8NnrwFjyYqf82mXe7xhJ5KDxfZe-O7D8amWI8Ow0U_RCU_5_rI47nRxoyGd7gpUjtz5RsJxM';

      var pusher = new Pusher('d995b7146329bb8422f3', {
        cluster: 'eu',
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                Authorization: 'Bearer ' + token,
            }
        }
      });

      var channel = pusher.subscribe('my-channel');
      channel.bind('my-event', function(data) {
        alert(JSON.stringify(data));
      });

      var channel2 = pusher.subscribe('private-chat.42');
      channel2.bind('private-message', function(data) {
        alert(JSON.stringify(data));
      });

    </script>
  </head>
  <body>
    <h1>Pusher Test</h1>
    <p>
      Try publishing an event to channel <code>my-channel</code>
      with event name <code>my-event</code>.
    </p>
  </body>
