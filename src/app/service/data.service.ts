import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, Subject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class DataService {

  constructor(public http: HttpClient) { }

  baseUrl = "http://localhost/Ticketeer/api/";

  private subject = new Subject<any>(); // For Realtime prevent refreshing && Pulling data automactically

  sendApiRequest(method: any, data: any) {
    return <any>(
      this.http.post(this.baseUrl + method, btoa(JSON.stringify(data)))
    );
  }

  sendApiRequest2(method: any, data: any, condition: any) {
    return <any>(
      this.http.post(
        this.baseUrl + method + condition,
        btoa(JSON.stringify(data))
      )
    );
  }

  getUpdate():Observable<any> { // For Constructor realtime pulling
    return this.subject.asObservable();
  }

  sendUpdate(message: string) {
    this.subject.next({ text: message });
  }

}
