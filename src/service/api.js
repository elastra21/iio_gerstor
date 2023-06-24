import axios from "axios";
import config from "../config";

var realIp = "";
export function SetIp(ip) {
  realIp = ip;
}

export default () => {
  const baseURL = "http://" + realIp + ":3000";
  return axios.create({
    baseURL,
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json"
    }
  });
};
