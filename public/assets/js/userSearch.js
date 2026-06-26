const dashboardUrl = "{{ route('dashboard') }}";
    let timer;

    function fetchUsers() {
        let search = document.getElementById('search').value;
        let status = document.getElementById('status').value;

        fetch(dashboardUrl + "?search=" + search + "&status=" + status, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('userTable').innerHTML = html;
        });
    }

    document.getElementById('search').addEventListener('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(() => fetchUsers(), 300);
    });

    document.getElementById('status').addEventListener('change', function () {
        fetchUsers();
    });