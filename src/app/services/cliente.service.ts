import { Injectable } from '@angular/core';
import { environment } from './../../environments/environment';
import { DataService } from './data.service';
import { HttpClient } from '@angular/common/http';

@Injectable()
export class ClienteService extends DataService {

  constructor(http: HttpClient) {
    super(environment.apiURL + "cliente/", http);
  }
  
}
