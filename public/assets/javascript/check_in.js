import QrScanner from '/assets/javascript/qr-scanner.min.js';

var checkinApp = new Vue({
    el: '#app',
    data: {
        attendees: [],
        searchTerm: '',
        searchResultsCount: 0,
        showScannerModal: false,
        workingAway: false,
        isInit: false,
        isScanning: false,
        videoElement: $('video#videoContainer')[0],
        scannerDataUrl: '',
        QrTimeout: null,
        scanner: null,
        successBeep: new Audio('/mp3/beep.mp3'),
        scanResult: false,
        scanResultObject: {}
    },

    created: function () {
        this.fetchAttendees()

        // Create QrScanner Object
        this.scanner = new QrScanner(
            this.videoElement,
            result => this.QrCheckin(result),
            {
                maxScansPerSecond: 10,
                highlightScanRegion: true
            }
        );
    },

    ready: function () {
    },

    methods: {
        fetchAttendees: function () {
            this.$http.post(Attendize.checkInSearchRoute, {q: this.searchTerm}).then(function (res) {
                this.attendees = res.data;
                this.searchResultsCount = (Object.keys(res.data).length);
                console.log('Successfully fetched attendees')
            }, function () {
                console.log('Failed to fetch attendees')
            });
        },
        toggleCheckin: function (attendee) {

            if(this.workingAway) {
                return;
            }
            this.workingAway = true;
            var that = this;


            var checkinData = {
                checking: attendee.has_arrived ? 'out' : 'in',
                attendee_id: attendee.id,
            };

            this.$http.post(Attendize.checkInRoute, checkinData).then(function (res) {
                if (res.data.status == 'success' || res.data.status == 'error') {
                    if (res.data.status == 'error') {
                        alert(res.data.message);
                    }
                    attendee.has_arrived = checkinData.checking == 'out' ? 0 : 1;
                    that.workingAway = false;
                } else {
                    /* @todo handle error*/
                    that.workingAway = false;
                }
            });
        },
        clearSearch: function () {
            this.searchTerm = '';
            this.fetchAttendees();
        },

        /* QR Scanner Methods */

        QrCheckin: function (attendeeReferenceCode) {

            this.isScanning = false;
            this.scanner.stop();

            this.$http.post(Attendize.qrcodeCheckInRoute, {attendee_reference: attendeeReferenceCode}).then(function (res) {
                this.successBeep.play();
                this.scanResult = true;
                this.scanResultObject = res.data;

            }, function (response) {
                this.scanResultObject.message = lang("whoops2");
            });
        },

        showQrModal: function () {
            this.showScannerModal = true;
            this.initScanner();
        },

        initScanner: function () {

            var that = this;
            this.isScanning = true;
            this.scanResult = false;

            /*
             If the scanner is already initiated clear it and start over.
             */
            if (this.isInit) {
                this.scanner.stop();
                this.scanner.start();
            }
            //alert(lang("checkin_init_error"));

            this.scanner.start();
            
        },
        closeScanner: function () {
            this.scanner.stop();
            this.showScannerModal = false;
            this.isInit = false;
            this.fetchAttendees();
        }
    }
});