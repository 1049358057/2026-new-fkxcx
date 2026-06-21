function getRequest(url) {
    var that = this;
    return new Promise(function(resolve, reject) {
        wx.request({
            url: getApp().globalData.request_url + url,
            header: {
                "content-type": "application/x-www-form-urlencoded"
            },
            method: "GET",
            success: function success(res) {
                resolve(res);
            },
            fail: function fail(res) {
                reject(res);
            }
        });
    });
}

module.exports = {
    getRequest: getRequest
};